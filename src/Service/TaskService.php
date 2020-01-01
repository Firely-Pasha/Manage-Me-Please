<?php


namespace App\Service;


use App\Entity\Company;
use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use App\Helpers\Serializer;
use App\Repository\CompanyRepository;
use App\Repository\ProjectRepository;
use App\Repository\SimpleTokenRepository;
use App\Repository\TaskListRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskService extends BaseService
{

    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * @var TaskListRepository
     */
    private $taskListRepository;

    public function __construct(Serializer $serializer, ValidatorInterface $validator,
                                ProjectRepository $projectRepository, SimpleTokenRepository $simpleTokenRepository,
                                CompanyRepository $companyRepository, TaskListRepository $taskListRepository,
                                UserRepository $userRepository, TaskRepository $taskRepository)
    {
        parent::__construct($serializer, $validator, $simpleTokenRepository, $companyRepository, $projectRepository, $userRepository);

        $this->taskListRepository = $taskListRepository;
        $this->taskRepository = $taskRepository;
    }

    public function getTask(/* REQUIRED: */ string $token, int $companyId, string $projectCode, string $taskId
        /* OPTIONAL: */): array
    {
        return $this->validateCompanyProject($token, $companyId, $projectCode,
            function (User $user, Company $company, Project $project) use ($taskId) {
                $task = $this->taskRepository->findOneBy([
                    'project' => $project,
                    'relativeId' => $taskId,
                    'deleted' => false
                ]);
                if ($task === null) {
                    return $this->createEntityNotFoundResponse('Task');
                }

                return $this->createSuccessfulResponse($this->getSerializer()->taskFull($task, true));
            }
        );
    }

    public function createTask( /* REQUIRED: */ string $token, int $companyId, string $projectCode, string $name,
        /* OPTIONAL: */ ?int $assignedToId, ?string $taskListId): array
    {
        return $this->validateCompanyProject($token, $companyId, $projectCode,
            function (User $user, Company $company, Project $project) use ($name, $assignedToId, $taskListId) {
                $assignedTo = $assignedToId ? $this->getUserRepository()->find($assignedToId) : null;
                if ($assignedToId !== null) {
                    if ($assignedTo === null || !$company->getEmployees()->contains($assignedTo)) {
                        return $this->createEntityNotFoundResponse('AssignedTo');
                    }
                    if (!$project->getUsers()->contains($assignedTo)) {
                        return $this->createResponse(['Assigned user is not in this project'], self::RESPONSE_CODE_FAIL_PERMISSION_DENIED);
                    }
                }

                $newTask = Task::create($project, $name, $user, $assignedTo);
                return $this->addEntity($this->taskRepository, $newTask,
                    function (Task $task) {
                        return $this->createSuccessfulResponse($this->getSerializer()->taskShort($task));
                    }
                );
            }
        );
    }
}