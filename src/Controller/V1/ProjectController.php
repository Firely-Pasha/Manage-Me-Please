<?php

namespace App\Controller\V1;

use App\Controller\Base\BaseController;
use App\Others\GetDataKeeper;
use App\Others\DataKeeper;
use App\Service\ProjectService;
use App\Service\SimpleTokenService;
use Couchbase\RegexpSearchQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProjectController
 * @package App\Controller\V1
 *
 * @Route("/v1", name="project_api")
 */
class ProjectController extends BaseController
{
    /**
     * @var ProjectService
     */
    protected $service;

    public function __construct(SimpleTokenService $simpleTokenService, ProjectService $service)
    {
        parent::__construct($simpleTokenService);
        $this->service = $service;
    }

    /**
     * @Route("/project", name="project_get", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getProject(Request $request): JsonResponse
    {
        return $this->handleGetData($request, true,
            function (DataKeeper $data, string $token) {
                return $this->service->getProject(
                    $token,
                    $data->getIntField('companyId'),
                    $data->getStringField('code')
                );
            }
        );
    }

    /**
     * @Route("/project", name="project_create", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function createProject(Request $request): JsonResponse
    {
        return $this->handlePostData($request, true,
            function (DataKeeper $data, $token) {
                return $this->service->createProject(
                    $token,
                    /* QUERY */
                    $data->getIntField('companyId'),
                    /* BODY */
                    $data->getStringField('code'),
                    $data->getStringField('name')
                );
            }
        );
    }

    /**
     * @Route("/project/users", name="project_users_get", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getUsers(Request $request): JsonResponse
    {
        return $this->handleGetData($request, true,
            function (DataKeeper $data, string $token) {
                return $this->service->getUsers(
                    $token,
                    $data->getIntField('companyId'),
                    $data->getStringField('code')
                );
            }
        );
    }

    /**
     * @Route("/project/users/add", name="project_users_add", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function addUser(Request $request): JsonResponse
    {
        return $this->handlePostData($request, true,
            function (DataKeeper $data, string $token) {
                return $this->service->addUser(
                    $token,
                    /* QUERY */
                    $data->getIntField('companyId'),
                    $data->getStringField('code'),
                    /* BODY */
                    $data->getIntField('userId')
                );
            }
        );
    }
}
