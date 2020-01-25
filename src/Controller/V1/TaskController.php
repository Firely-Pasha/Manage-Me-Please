<?php

namespace App\Controller\V1;

use App\Controller\Base\BaseController;
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
     * @Route("/task/{id}", name="task_get")
     * @param int $id
     * @return JsonResponse
     */
    public function index(int $id): JsonResponse
    {
        $result = $this->service->getTask($id);
        return $this->createJsonResponse($result);
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
                    $data->getIntField('assignedToId', true)
                );
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
