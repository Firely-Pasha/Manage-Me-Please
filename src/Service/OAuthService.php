<?php


namespace App\Service;


use App\Helpers\Serializer;
use App\Repository\OAuthTokenRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OAuthService extends BaseService
{
    private $oAuthTokenRepository;

    public function __construct(Serializer $serializer, ValidatorInterface $validator, OAuthTokenRepository $oAuthTokenRepository)
    {
        parent::__construct($serializer, $validator);
        $this->oAuthTokenRepository = $oAuthTokenRepository;
    }

    public function createClient(string $token, string $clientName, string $redirectUri)
    {

    }

    public function checkToken(string $token)
    {
        return $this->createResponse($this->getSerializer()->createSimpleResponse($this->oAuthTokenRepository->find($token)->getUser()->getId()), self::RESPONSE_CODE_SUCCESS);
    }
}