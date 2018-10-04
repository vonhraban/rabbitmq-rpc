<?php

namespace Datix\User;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQUserStore implements UserStore
{
    /**
     * @var AMQPStreamConnection
     */
    private $connection;
    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel
     */
    private $channel;
    /**
     * @var
     */
    private $callback_queue;

    /**
     * @var string
     */
    private $correlationId;
    /**
     * @var
     */
    private $response;
    /**
     * @var string
     */
    private $queue_name;

    /**
     * RabbitMQUserStore constructor.
     * @param AMQPStreamConnection $connection
     * @param $queue_name
     */
    public function __construct(AMQPStreamConnection $connection, $queue_name)
    {
        $this->connection = $connection;
        $this->channel = $this->connection->channel();
        list($this->callback_queue, ,) = $this->channel->queue_declare(
            "",
            false,
            false,
            true,
            false
        );
        $this->channel->basic_consume(
            $this->callback_queue,
            '',
            false,
            false,
            false,
            false,
            array(
                $this,
                'onResponse'
            )
        );
        $this->queue_name = $queue_name;
    }

    /**
     * Callback to be called when response is received
     * @param $rep
     */
    public function onResponse($rep)
    {
        if ($rep->get('correlation_id') == $this->correlationId) {
            $this->response = $rep->body;
        }
    }

    /**
     * Get user details by id
     *
     * @param string $id
     * @return array User details
     *
     * @throws \Exception if User not found or some other error happened
     */
    public function get($id)
    {
        $this->response = null;
        $this->correlationId = uniqid();

        $msg = new AMQPMessage(
            $id,
            array(
                'correlation_id' => $this->correlationId,
                'reply_to' => $this->callback_queue
            )
        );
        $this->channel->basic_publish($msg, '', $this->queue_name);
        while (!$this->response) {
            $this->channel->wait();
        }

        $decoded_response = json_decode($this->response, true);
        if(isset($decoded_response['error']))
        {
            switch($decoded_response['error'])
            {
                case 'Not found':
                    throw new \Exception("User not found");
                default:
                    throw new \Exception("Unexpected error");
            }
        }

        return $this->response;
    }
}
