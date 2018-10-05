<?php
namespace Datix\User;

interface UserStore
{
    /**
     * Get user details by id
     *
     * @param int $id
     * @return User Response parsed into User
     *
     * @throws MalformedResponseException if the response is not a valid JSON
     * @throws UserNotFoundException if user not found
     * @throws GenericException if something else is wrong and we do not what
     */
    public function get(int $id): User;
}