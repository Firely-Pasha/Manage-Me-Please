<?php


namespace App\Exceptions;


class AccessException extends MmpException
{
    public function __construct($message = "Access denied")
    {
        parent::__construct($message, self::RESPONSE_CODE_FAIL_PERMISSION_DENIED);
    }
}