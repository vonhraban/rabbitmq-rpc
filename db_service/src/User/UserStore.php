<?php
namespace Datix\Server\User;

interface UserStore
{
    /**
     * Get a user by id
     *
     * @param int $id Id to search against
     *
     * @return User The user
     *
     * @throws \OutOfBoundsException if ID is negative
     */
    public function get(int $id): User;
}