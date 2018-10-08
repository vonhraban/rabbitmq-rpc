<?php

require_once __DIR__ . '/vendor/autoload.php';
$config = require_once 'config.php';

use Datix\Server\Listener\RabbitMQListener;
use Datix\Server\MessageHandler;
use Datix\Server\User\CSVUserStore;
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection(
    $config['rabbitmq']['host'],
    $config['rabbitmq']['port'],
    $config['rabbitmq']['user'],
    $config['rabbitmq']['password']
);

$userStore = new CSVUserStore($config['user_store']['source_csv']);
$messageHandler = new MessageHandler($userStore);

$listener = new RabbitMQListener($connection, $config['rabbitmq']['queue_name']);
echo "[x] Listening to connections...";
$listener->listen('getUser', [$messageHandler, 'findUsers']);
