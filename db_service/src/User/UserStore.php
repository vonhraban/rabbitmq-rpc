<?php
namespace Datix\Server\User;

interface UserStore
{
    /**
     * Get a user by id
     *
     * @param int $id Id to search against
     *
     * @return array The user
     */
    public function get($id);
}