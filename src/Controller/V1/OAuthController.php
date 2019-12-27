<?php

namespace App\Controller\V1;

use App\Controller\Base\BaseController;
use App\Entity\db\OAuthClient;
use App\Service\OAuthService;
use DateInterval;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OAuthController
 * @package App\Controller\V1
 * @Route("/oauth", name="oauth")
 */
class OAuthController extends BaseController
{
    protected $service;

    public function __construct(OAuthService $oAuthService)
    {
        parent::__construct($oAuthService);
        $this->service = $oAuthService;
    }

    /**
     * @Route("/client", name="create_client", methods={"POST"})
     */
    public function createClient(Request $request)
    {
        return $this->handlePostData($request, true, function ($data) {
            return $this->service->checkToken('asd');
        });
    }
}
