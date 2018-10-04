<?php

require_once __DIR__ . '/vendor/autoload.php';
$config = require_once 'config.php';

use Datix\Server\User\CSVUserStore;
use Datix\Server\User\UserNotFoundException;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection(
    $config['rabbitmq']['host'],
    $config['rabbitmq']['port'],
    $config['rabbitmq']['user'],
    $config['rabbitmq']['password']
);

$userStore = new CSVUserStore($config['user_store']['source_csv']);

$channel = $connection->channel();

$channel->queue_declare($config['rabbitmq']['queue_name'], false, false, false, false);

echo "Awaiting RPC requests\n";
$callback = function ($req) use ($userStore) {
    echo 'Received ', $req->body, "\n";
    try {
        $user = $userStore->get((int)$req->body);

        $msg = new AMQPMessage(
            json_encode($user),
            array('correlation_id' => $req->get('correlation_id'))
        );

    } catch (UserNotFoundException $e) {
        $msg = new AMQPMessage(
            json_encode(['error' => "Not found"]),
            array('correlation_id' => $req->get('correlation_id'))
        );
    } catch (\Exception $e) {
        $msg = new AMQPMessage(
            json_encode(['error' => "Unhandled error occurred"]),
            array('correlation_id' => $req->get('correlation_id'))
        );
    }

    $req->delivery_info['channel']->basic_publish(
        $msg,
        '',
        $req->get('reply_to')
    );

    $req->delivery_info['channel']->basic_ack(
        $req->delivery_info['delivery_tag']
    );
};

$channel->basic_qos(null, 1, null);
$channel->basic_consume($config['rabbitmq']['queue_name'], '', false, false, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}

$channel->close();
$connection->close();
