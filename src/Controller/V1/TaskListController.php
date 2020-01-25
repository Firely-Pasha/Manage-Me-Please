<?php

namespace App\Controller\V1;

use App\Controller\Base\BaseController;
use App\Others\DataKeeper;
use App\Service\SimpleTokenService;
use App\Service\TaskListService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1", name="task_list_api")
 * Class TaskListController
 * @package App\Controller\V1
 */
class TaskListController extends BaseController
{
    /**
     * @var TaskListService
     */
    protected $service;

    public function __construct(SimpleTokenService $simpleTokenService, TaskListService $taskListService)
    {
        parent::__construct($simpleTokenService);

        $this->service = $taskListService;
    }

    /**
     * @Route("/task-list", name="task_list_get", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getTaskList(Request $request): JsonResponse
    {
        return $this->handleGetData($request, true,
            function (DataKeeper $dataKeeper, string $token) {
                return $this->service->getTaskList(
                    $token,
                    $dataKeeper->getIntField('companyId'),
                    $dataKeeper->getStringField('projectCode'),
                    $dataKeeper->getIntField('taskListId')
                );
            }
        );
    }

    /**
     * @Route("/task-list", name="task_list_create", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function createTaskList(Request $request): JsonResponse
    {
        return $this->handlePostData($request, true,
            function (DataKeeper $data, string $token) {
                return $this->service->createTaskList(
                    $token,
                    /* QUERY */
                    $data->getIntField('companyId'),
                    $data->getStringField('projectCode'),
                    /* BODY */
                    $data->getStringField('name')
                );
            }
        );
    }
}
