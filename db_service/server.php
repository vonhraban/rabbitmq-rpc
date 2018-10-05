<?php

require_once __DIR__ . '/vendor/autoload.php';
$config = require_once 'config.php';

use Datix\Server\Listener\RabbitMQListener;
use Datix\Server\User\CSVUserStore;
use Datix\Server\User\UserNotFoundException;
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection(
    $config['rabbitmq']['host'],
    $config['rabbitmq']['port'],
    $config['rabbitmq']['user'],
    $config['rabbitmq']['password']
);

function findUsers(array $payload) { // This message could be also a nice wrapper class
    global $config; // TODO this is not nice

    // some fancy validation logic could go here

    $userStore = new CSVUserStore($config['user_store']['source_csv']);
    try {
        $user = $userStore->get((int)$payload['id']);

        return $user->toArray();

    } catch (UserNotFoundException $e) {
        return ['error' => "Not found"];
    } catch (\Exception $e) {
        return ['error' => "Unhandled error occurred"];
    }
}

$listener = new RabbitMQListener($connection, $config['rabbitmq']['queue_name']);
$listener->listen('getUser', 'findUsers');
