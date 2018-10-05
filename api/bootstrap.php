<?php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Datix\User\RabbitMQUserStore;

$config = require_once 'config.php';

$c = new \Slim\Container();

$c['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        return $c['response']->withStatus(500)
            ->withHeader('Content-Type', 'application/json');
    };
};

$connection = new AMQPStreamConnection(
    $config['rabbitmq']['host'],
    $config['rabbitmq']['port'],
    $config['rabbitmq']['user'],
    $config['rabbitmq']['password']
);

$c['user_store'] = new RabbitMQUserStore($connection, $config['rabbitmq']['queue_name']);


return new \Slim\App($c);
