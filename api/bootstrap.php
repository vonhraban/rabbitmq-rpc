<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Datix\User\RabbitMQUserStore;

const QUEUE_NAME = 'rpc_queue';

$c = new \Slim\Container();

$c['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        return $c['response']->withStatus(500)
            ->withHeader('Content-Type', 'application/json');
    };
};


$rpcConnection = new AMQPStreamConnection(
    'rabbitmq',
    5672,
    'guest',
    'guest'
);

$c['user_store'] = new RabbitMQUserStore($rpcConnection, QUEUE_NAME);


return new \Slim\App($c);
