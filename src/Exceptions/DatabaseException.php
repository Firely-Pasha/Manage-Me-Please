<?php


namespace App\Exceptions;


class DatabaseException extends MmpException
{
    public function __construct($message = "")
    {
        parent::__construct($message, self::RESPONSE_CODE_FAIL_DATABASE);
    }
}