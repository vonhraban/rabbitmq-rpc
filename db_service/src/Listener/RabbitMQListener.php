<?php
namespace Datix\Server\Listener;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQListener {
    /**
     * @var callable User defined callback to be executed upon a new message
     */
    private $userCallback;

    /**
     * RabbitMQListener constructor
     * @param AMQPStreamConnection $connection RabbitMQ connection
     * @param string $queue_name Name of the queue to listen to
     */
    public function __construct(AMQPStreamConnection $connection, $queue_name)
    {
        $this->queue_name = $queue_name;
        $this->channel = $connection->channel();
        $this->channel->queue_declare($this->queue_name, false, false, false, false);
    }

    /**
     * Start listening to incoming messages
     *
     * @param callable $userCallback Callback to be executed when the new message arrives
     *                              function(string $messageBody)
     */
    public function listen($userCallback /* , $messageType */) { // this will create a map of type to callback
        $this->userCallback = $userCallback;

        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume($this->queue_name, '', false, false, false, false, [$this, 'messageCallback']);


        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

    /**
     * Callback (do not confuse with user-defined one) that wraps the needed response into the message
     * and replies to the channel
     *
     * @param AMQPMessage $req Received request message
     */
    public function messageCallback(AMQPMessage $req) {
        echo 'Received ', $req->body, "\n";

        $receivedMessage = json_decode($req->body, true);

        // did we decode correctly?
        if ($receivedMessage === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new MalformedMessageException($req->body);
        }

        // is the structure as expected?
        if(!isset($receivedMessage['command']) || !isset($receivedMessage['payload']))
        {
            throw new MalformedMessageException($req->body);

        }

        $responseMessage = call_user_func(
            $this->userCallback,
            $receivedMessage['command'],
            $receivedMessage['payload']
        );

        if($responseMessage === false) {
            // do not do anything if the callback is not subscribed to that command
            return;
        }

        // TODO! only ack is the rezponse is not false
        // create message
        $msg = new AMQPMessage(
            json_encode($responseMessage),
            array('correlation_id' => $req->get('correlation_id'))
        );

        $req->delivery_info['channel']->basic_publish(
            $msg,
            '',
            $req->get('reply_to')
        );

        $req->delivery_info['channel']->basic_ack(
            $req->delivery_info['delivery_tag']
        );
    }
}