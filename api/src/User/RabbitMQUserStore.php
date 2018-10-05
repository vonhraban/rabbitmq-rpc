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
     * @param AMQPMessage $rep
     */
    public function onResponse(AMQPMessage $rep)
    {
        if ($rep->get('correlation_id') == $this->correlationId) {
            $this->response = $rep->body;
        }
    }

    /**
     * Get user details by id
     *
     * @param int $id
     * @return User Response parsed into User
     *
     * @throws MalformedResponseException if the response is not a valid JSON
     * @throws UserNotFoundException if user not found
     * @throws GenericException if something else is wrong and we do not what
     */
    public function get(int $id): User
    {
        $this->response = null;
        $this->correlationId = uniqid();

        $msg = new AMQPMessage(
            json_encode([
                'command' => 'getUser',
                'payload' => [
                    'id' => $id
                ]
            ]),
            array(
                'correlation_id' => $this->correlationId,
                'reply_to' => $this->callback_queue
            )
        );
        $this->channel->basic_publish($msg, '', $this->queue_name);
        while (!$this->response) {
            $this->channel->wait();
        }


        return $this->parseResponse($this->response);
    }

    /**
     * Parse and validate the response and throw exception is anything is not as expected
     *
     * @param string $rawResponse Raw response from RabbitMQ
     *
     * @return User Response parsed into User
     *
     * @throws MalformedResponseException if the response is not a valid JSON or is of unexpected structure
     * @throws UserNotFoundException if user not found
     * @throws GenericException if something else is wrong and we do not what
     */
    protected function parseResponse(string $rawResponse): User
    {
        $decodedResponse = json_decode($rawResponse, true);

        if ($decodedResponse === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new MalformedResponseException($rawResponse);
        }

        if (!isset($decodedResponse['type'])) {
            throw new MalformedResponseException($rawResponse);
        }

        $type = $decodedResponse['type'];

        switch ($type) {
            case 'UserData':
                return User::fromArray($decodedResponse['payload']);
            case 'UserNotFound':
                throw new UserNotFoundException();
            case 'UnexpectedError':
                throw new GenericException();
        }
    }
}
