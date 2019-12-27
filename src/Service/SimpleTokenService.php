<?php


namespace App\Service;


use DateTime;

class SimpleTokenService extends BaseService
{
    public function checkTokenForErrors(string $token): array
    {
        $simpleToken = $this->getSimpleTokenRepository()->find($token);
        if ($simpleToken === null) {
            return [self::RESPONSE_CODE_FAIL_AUTHORIZATION, ['Invalid token']];
        }

        $tokenExpirationDate = $simpleToken->getExpiresIn()->getTimestamp();
        $currentDate = (new DateTime())->getTimestamp();
        if ($tokenExpirationDate < $currentDate) {
            return [self::RESPONSE_CODE_FAIL_AUTHORIZATION_TOKEN_EXPIRED, ['Token expired']];
        }

        if ($simpleToken->isRevoked()) {
            return [self::RESPONSE_CODE_FAIL_AUTHORIZATION_TOKEN_REVOKED, ['Token revoked']];
        }

        return [];
    }
}