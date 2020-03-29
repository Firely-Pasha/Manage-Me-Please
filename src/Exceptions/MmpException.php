<?php


namespace App\Exceptions;


use Exception;

class MmpException extends Exception
{
    public const RESPONSE_CODE_SUCCESS = 1;
    public const RESPONSE_CODE_FAIL_JSON = 16;
    public const RESPONSE_CODE_FAIL_DATABASE = 17;
    public const RESPONSE_CODE_FAIL_ENTITY_NOT_FOUND = 18;
    public const RESPONSE_CODE_FAIL_VALIDATION = 19;
    public const RESPONSE_CODE_FAIL_AUTHORIZATION = 20;
    public const RESPONSE_CODE_FAIL_AUTHORIZATION_TOKEN_EXPIRED = 21;
    public const RESPONSE_CODE_FAIL_AUTHORIZATION_TOKEN_REVOKED = 22;
    public const RESPONSE_CODE_FAIL_PERMISSION_DENIED = 23;
    public const RESPONSE_CODE_FAIL_ALREADY_EXISTS = 24;

    /**
     * @var array
     */
    private $additionalInfo;

    /**
     * MmpException constructor.
     * @param string $message
     * @param int $code
     * @param array $additionalInfo
     */
    public function __construct($message = "", $code = 0, $additionalInfo = [])
    {
        parent::__construct($message, $code);
        $this->additionalInfo = $additionalInfo;
    }

    /**
     * @return array
     */
    public function getAdditionalInfo(): array
    {
        return $this->additionalInfo;
    }
}