<?php
namespace Datix\User;

use Throwable;

class GenericException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct("Unexpected error");
    }
}