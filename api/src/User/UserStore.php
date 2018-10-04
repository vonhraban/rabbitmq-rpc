<?php
namespace Datix\User;

interface UserStore
{
    /**
     * Get user details by id
     *
     * @param string $id
     * @return array User details
     *
     * @throws \Exception if User not found or some other error happened
     */
    public function get($id);
}