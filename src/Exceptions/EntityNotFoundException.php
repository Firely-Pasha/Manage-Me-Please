<?php


namespace App\Exceptions;


class EntityNotFoundException extends MmpException
{
    private const MESSAGE = ' not found';

    public function __construct($entity = "")
    {
        parent::__construct($entity . self::MESSAGE, self::RESPONSE_CODE_FAIL_ENTITY_NOT_FOUND);
    }
}