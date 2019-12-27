<?php

namespace App\Controller\V1;

use App\Controller\Base\BaseController;
use App\Others\DataKeeper;
use App\Service\CompanyService;
use App\Service\SimpleTokenService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CompanyController
 * @package App\Controller\V1
 *
 * @Route("/v1", name="company_api")
 */
class CompanyController extends BaseController
{
    protected $service;

    public function __construct(SimpleTokenService $simpleTokenService, CompanyService $service)
    {
        parent::__construct($simpleTokenService);
        $this->service = $service;
    }

    /**
     * @Route("/company", name="company_get", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getCompany(Request $request): JsonResponse
    {
        return $this->handleGetData($request, true,
            function (DataKeeper $data, string $token) {
                return $this->service->getCompany(
                    $token,
                    $data->getIntField('id')
                );
            }
        );
    }

    /**
     * @Route("/company", name="company_create", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        return $this->handlePostData($request, true,
            function (DataKeeper $data, $token) {
                return $this->service->createCompany(
                    $token,
                    $data->getStringField('name')
                );
            }
        );
    }

    /**
     * @Route("/company/employees", name="company_employees", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getEmployees(Request $request): JsonResponse
    {
        return $this->handleGetData($request, true,
            function (DataKeeper $data, string $token) {
                return $this->service->getEmployees(
                    $token,
                    $data->getStringField('id')
                );
            }
        );
    }

    /**
     * @Route("/company/employees", name="company_employees_add", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function addEmployee(Request $request): JsonResponse
    {
        return $this->handlePostData($request, true,
            function (DataKeeper $data, $token) {
                return $this->service->addEmployee(
                    $token,
                    $data->getIntField('id'),
                    $data->getIntField('userId')
                );
            }
        );
    }

    /**
     * @Route("/company/projects", name="company_projects", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getProjects(Request $request): JsonResponse
    {
        return $this->handleGetData($request, true,
            function (DataKeeper $data, string $token) {
                return $this->service->getCompanyProjects(
                    $token,
                    $data->getIntField('id')
                );
            }
        );
    }
}
