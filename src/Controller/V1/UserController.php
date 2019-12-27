<?php

namespace App\Controller\V1;

use App\Controller\Base\BaseController;
use App\Others\DataKeeper;
use App\Service\SimpleTokenService;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller\V1
 *
 * @Route("/v1", name="api_v1_user")
 */
class UserController extends BaseController
{
    /**
     * @var UserService
     */
    public $service;

    public function __construct(SimpleTokenService $simpleTokenService, UserService $service)
    {
        parent::__construct($simpleTokenService);
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/user/register", name="user_register", methods={"POST"})
     */
    public function register(Request $request): JsonResponse
    {
        return $this->handlePostData($request, false,
            function (DataKeeper $data) {
                return $this->service->register(
                    $data->getStringField('login'),
                    $data->getStringField('email'),
                    $data->getStringField('password'),
                    $data->getStringField('name'),
                    $data->getStringField('surname')
                );
            }
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/user/login", name="user_login", methods={"POST"})
     */
    public function login(Request $request): JsonResponse
    {
        return $this->handlePostData($request, false,
            function (DataKeeper $data) {
                return $this->service->login(
                    $data->getStringField('login'),
                    $data->getStringField('password')
                );
            }
        );
    }

    /**
     * @Route("/user", name="user_get", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserInfo(Request $request): JsonResponse
    {
        return $this->handleGetData($request, false,
            function (DataKeeper $data, ?string $token) {
                return $this->service->getUser(
                    $token,
                    $data->getIntField('id', true)
                );
            }
        );
    }

    /**
     * @Route("/user/tasks", name="user_tasks_get", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getTasks(Request $request): JsonResponse
    {
        return $this->handleGetData($request, true,
            function (DataKeeper $data, string $token) {
                return $this->service->getUserTasks(
                    $token,
                    $data->getIntField('id', true)
                );
            }
        );
    }

    /**
     * @Route("/user/companies", name="user_companies_get", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getCompanies(Request $request): JsonResponse
    {
        return $this->handleGetData($request, true,
            function (DataKeeper $data, string $token) {
                return $this->service->getUserCompanies(
                    $token,
                    $data->getIntField('id', true)
                );
            }
        );
    }

    /**
     * @Route("/user/projects", name="user_projects_get", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getProjects(Request $request): JsonResponse
    {
        return $this->handleGetData($request, true,
            function (DataKeeper $data, string $token) {
                return $this->service->getUserProjects(
                    $token,
                    $data->getIntField('id', true)
                );
            }
        );
    }
}
