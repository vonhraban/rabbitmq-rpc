<?php
namespace Datix\User;

class UserNotFoundException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct("User not found");
    }

}