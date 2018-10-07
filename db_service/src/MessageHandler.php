<?php
namespace Datix\Server;

use Datix\Server\User\CSVUserStore;
use Datix\Server\User\UserNotFoundException;
use Datix\Server\User\UserStore;

class MessageHandler {

    /**
     * @var UserStore
     */
    private $userStore;

    /**
     * MessageHandler constructor.
     * @param UserStore $userStore
     */
    public function __construct(UserStore $userStore)
    {
        $this->userStore = $userStore;
    }

    /**
     * Find user by given id
     *
     * @param array $payload Message payload
     *
     * @return array Response to be sent
     */
    public function findUsers(array $payload): array { // This message could be also a nice wrapper class
        // some fancy validation logic could go here
        try {
            $user = $this->userStore->get((int)$payload['id']);

            return [
                'type' => 'UserData',
                'payload' => $user->toArray(),
            ];

        } catch (UserNotFoundException $e) {
            return ['type' => "UserNotFound"];
        } catch (\Exception $e) {
            return ['type' => "UnexpectedError"];
        }
    }
}
