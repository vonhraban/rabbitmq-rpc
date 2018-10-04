<?php

require_once '../vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Datix\User\RabbitMQUserStore;


require '../vendor/autoload.php';
$app = require '../bootstrap.php';

$app->get('/getUser/{id}', function (Request $request, Response $response, array $args) {
    /** @var RabbitMQUserStore $rpc_connection */
    $userStore = $this->get('user_store');

    $id = $args['id'];
    $response->withHeader('Content-type', 'application/json')
        ->getBody()
        ->write(json_encode($userStore->get($id)));

    return $response;
});

$app->run();