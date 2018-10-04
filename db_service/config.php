<?php
return [
    'rabbitmq' => [
        'host' => 'rabbitmq',
        'port' => 5672,
        'user' => 'guest',
        'password' => 'guest',
        'queue_name' => 'rpc_queue',
    ],
    'user_store' => [
        'source_csv' => __DIR__ . '/user_data.csv',
    ]
];