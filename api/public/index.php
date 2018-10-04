<?php

require_once '../vendor/autoload.php';

use Datix\User\MalformedResponseException;
use Datix\User\UserNotFoundException;
use Datix\User\UserStore;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Datix\User\RabbitMQUserStore;


require '../vendor/autoload.php';
$app = require '../bootstrap.php';

$app->get('/getUser/{id}', function (Request $request, Response $response, array $args) {
    /** @var UserStore $userStore */
    $userStore = $this->get('user_store');
    $id = $args['id'];

    $response = $response->withHeader('Content-type', 'application/json');
    /** @var RabbitMQUserStore $rpc_connection */
    try {
        $user = $userStore->get($id);
        return $response->withStatus(200)
            ->getBody()
            ->write(json_encode($user->toArray()));
    } catch (UserNotFoundException $e) {
        return $response->withStatus(404);
    } catch (MalformedResponseException $e) {
        return $response->withStatus(502);
    } catch (\Exception $e) {
        return $response->withStatus(500);
    }
});

$app->run();