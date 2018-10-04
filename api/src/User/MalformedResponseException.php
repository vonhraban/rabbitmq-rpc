<?php
namespace Datix\User;

class MalformedResponseException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct("Response is not a valid JSON");
    }

}