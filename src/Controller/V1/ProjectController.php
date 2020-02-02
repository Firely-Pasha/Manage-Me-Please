<?php

namespace App\Controller\V1;

use App\Controller\Base\BaseController;
use App\Helpers\Constants;
use App\Others\DataKeeper;
use App\Service\ProjectService;
use App\Service\SimpleTokenService;
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
     * @Route("/project/task-lists/sort", name="project_task_lists_sort", methods={"POST"})
     */
    public function sortTaskList(Request $request)
    {
        return $this->handlePostData($request, true,
            function (DataKeeper $data, $token) {
                return $this->service->sortTaskLists(
                    $token,
                    $data->getIntField(Constants::PARAM_COMPANY_ID),
                    $data->getStringField(Constants::PARAM_PROJECT_CODE),
                    $data->getAssocField(Constants::BODY_SORT, true),
                    $data->getIntArrayField(Constants::BODY_DELETIONS)
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

    /**
     * @Route("/project/tags", name="project_tags_get", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getTags(Request $request): JsonResponse
    {
        return $this->handleGetData($request, true,
            function (DataKeeper $data, string $token) {
                return $this->service->getTags(
                    $token,
                    // QUERY
                    $data->getIntField(Constants::PARAM_COMPANY_ID),
                    $data->getStringField(Constants::PARAM_PROJECT_CODE)
                );
            }
        );
    }

    /**
     * @Route("/project/tags", name="project_tags_add", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function addTag(Request $request): JsonResponse
    {
        return $this->handlePostData($request, true,
            function (DataKeeper $data, string $token) {
                return $this->service->addTag(
                    $token,
                    $data->getIntField(Constants::PARAM_COMPANY_ID),
                    $data->getStringField(Constants::PARAM_PROJECT_CODE),

                    $data->getStringField(Constants::BODY_TAG_TITLE),
                    $data->getStringField(Constants::BODY_TAG_COLOR_CODE),
                    $data->getStringField(Constants::BODY_TAG_GROUP_ID, true)
                );
            }
        );
    }

    /**
     * @Route("/project/tags", name="project_tags_delete", methods={"DELETE"})
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteTag(Request $request): JsonResponse
    {
        return $this->handleGetData($request, true,
            function (DataKeeper $data, string $token) {
                return $this->service->delete(
                    $token,
                    $data->getIntField(Constants::PARAM_COMPANY_ID),
                    $data->getStringField(Constants::PARAM_PROJECT_CODE),
                    $data->getIntField(Constants::PARAM_TAG_ID)
                );
            }
        );
    }
}
