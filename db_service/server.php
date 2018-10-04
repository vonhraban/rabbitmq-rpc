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

function findUsers($messageBody) { // TODO! Make this an array of payload of stuff etc
    print_r($messageBody);
    global $config; // TODO this is not nice
    $userStore = new CSVUserStore($config['user_store']['source_csv']);
    try {
        $user = $userStore->get((int)$messageBody);

        return $user;

    } catch (UserNotFoundException $e) {
        return ['error' => "Not found"];
    } catch (\Exception $e) {
        return ['error' => "Unhandled error occurred"];
    }
}

$listener = new RabbitMQListener($connection, $config['rabbitmq']['queue_name']);
$listener->listen('findUsers');
