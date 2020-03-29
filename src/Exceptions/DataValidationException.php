<?php


namespace App\Exceptions;


class DataValidationException extends MmpException
{
    public function __construct($validationList = [])
    {
        parent::__construct('Data validation error', self::RESPONSE_CODE_FAIL_VALIDATION, $validationList);
    }
}