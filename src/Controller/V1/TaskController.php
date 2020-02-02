<?php

namespace App\Controller\V1;

use App\Controller\Base\BaseController;
use App\Helpers\Constants;
use App\Others\DataKeeper;
use App\Service\SimpleTokenService;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1", name="task_api")
 * Class TaskController
 * @package App\Controller\V1
 */
class TaskController extends BaseController
{
    /**
     * @var TaskService
     */
    protected $service;

    public function __construct(SimpleTokenService $simpleTokenService, TaskService $taskService)
    {
        parent::__construct($simpleTokenService);

        $this->service = $taskService;
    }

    /**
     * @Route("/task", name="project_task_get", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getTask(Request $request): JsonResponse
    {
        return $this->handleGetData($request, true,
            function (DataKeeper $data, string $token) {
                return $this->service->getTask(
                    $token,
                    $data->getIntField('companyId'),
                    $data->getStringField('projectCode'),
                    $data->getIntField('taskId')
                );
            }
        );
    }

    /**
     * @Route("/task", name="project_task_create", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function createTask(Request $request): JsonResponse
    {
        return $this->handlePostData($request, true,
            function (DataKeeper $data, string $token) {
                return $this->service->createTask(
                    $token,
                    /* QUERY */
                    $data->getIntField('companyId'),
                    $data->getStringField('projectCode'),
                    /* BODY */
                    $data->getStringField('name'),
                    $data->getIntField('taskListId'),
                    $data->getIntField('assignedToId', true),
                    $data->getStringField('description', true),
                    $data->getIntArrayField(Constants::BODY_TAG_IDS, true)
                );
            }
        );
    }

    /**
     * @Route("/task/tags", name="task_tag_add", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function addTag(Request $request): JsonResponse
    {
        return $this->handlePostData($request, true,
            function (DataKeeper $data, string $token) {
                return $this->service->addTag(
                    $token,
                    /* QUERY */
                    $data->getIntField(Constants::PARAM_COMPANY_ID),
                    $data->getStringField(Constants::PARAM_PROJECT_CODE),
                    $data->getStringField(Constants::PARAM_TASK_ID),
                    /* BODY */
                    $data->getIntArrayField(Constants::BODY_TAG_IDS));
            }
        );
    }


    /**
     * @Route("/task/tags", name="task_tag_remove", methods={"DELETE"})
     * @param Request $request
     * @return JsonResponse
     */
    public function removeTag(Request $request): JsonResponse
    {
        return $this->handleGetData($request, true,
            function (DataKeeper $data, string $token) {
                return $this->service->remove(
                    $token,
                    /* QUERY */
                    $data->getIntField(Constants::PARAM_COMPANY_ID),
                    $data->getStringField(Constants::PARAM_PROJECT_CODE),
                    $data->getStringField(Constants::PARAM_TASK_ID),
                    $data->getIntArrayField(Constants::PARAM_TAG_IDS));
            }
        );
    }

//    /**
//     * @Route(/task/list)
//     * @param Request $request
//     */
//    public function getTaskList(Request $request)
//    {
//        return $this->handleGetData($request, true,
//            function (DataKeeper $data, string $token) {
//                return $this->service->getList();
//            }
//        );
//    }
}
