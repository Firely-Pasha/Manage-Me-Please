<?php


namespace App\Service;


use App\Entity\Company;
use App\Entity\Project;
use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\User;
use App\Helpers\Serializer;
use App\Repository\CompanyRepository;
use App\Repository\ProjectRepository;
use App\Repository\SimpleTokenRepository;
use App\Repository\TagRepository;
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

    /**
     * @var TagRepository
     */
    private $tagRepository;

    public function __construct(Serializer $serializer, ValidatorInterface $validator,
                                ProjectRepository $projectRepository, SimpleTokenRepository $simpleTokenRepository,
                                CompanyRepository $companyRepository, TaskListRepository $taskListRepository,
                                UserRepository $userRepository, TaskRepository $taskRepository, TagRepository $tagRepository)
    {
        parent::__construct($serializer, $validator, $simpleTokenRepository, $companyRepository, $projectRepository, $userRepository);

        $this->taskListRepository = $taskListRepository;
        $this->taskRepository = $taskRepository;
        $this->tagRepository = $tagRepository;
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

                return $this->createSuccessfulResponse($this->getSerializer()->taskFull($task, false));
            }
        );
    }

    public function createTask(
        /* REQUIRED: */ string $token, int $companyId, string $projectCode, string $name, int $taskListId,
        /* OPTIONAL: */ ?int $assignedToId, ?string $description, ?array $tagIds
    ): array
    {
        return $this->validateCompanyProject($token, $companyId, $projectCode,
            function (User $user, Company $company, Project $project) use ($name, $assignedToId, $taskListId, $description, $tagIds) {
                // ASSIGMENT
                $assignedTo = $assignedToId ? $this->getUserRepository()->find($assignedToId) : null;
                if ($assignedToId !== null) {
                    if ($assignedTo === null || !$company->getEmployees()->contains($assignedTo)) {
                        return $this->createEntityNotFoundResponse('AssignedTo');
                    }
                    if (!$project->getUsers()->contains($assignedTo)) {
                        return $this->createResponse(['Assigned user is not in this project'], self::RESPONSE_CODE_FAIL_PERMISSION_DENIED);
                    }
                }

                // TASKLIST
                $taskList = $taskListId ? $this->taskListRepository->findOneByRelativeId($project, $taskListId) : null;
                if ($taskList === null) {
                    return $this->createEntityNotFoundResponse('TaskList');
                }

                // TAGS
                $tags = $this->tagRepository->findBy(['project' => $project, 'relativeId' => $tagIds]);

                // CREATION
                $newTask = Task::create($project, $name, $user, $taskList, $assignedTo, $description, $tags);
                return $this->addEntity($this->taskRepository, $newTask,
                    function (Task $task) {
                        return $this->createSuccessfulResponse($this->getSerializer()->taskShort($task));
                    }
                );
            }
        );
    }

    /**
     * @param string $token
     * @param int $companyId
     * @param string $projectCode
     * @param int $taskId
     * @param int[] $tagIds
     * @return array
     */
    public function addTag(
        /* REQUIRED: */ string $token, int $companyId, string $projectCode, int $taskId, array $tagIds
    ): array
    {
        return $this->validateCompanyProjectTask($token, $companyId, $projectCode, $taskId,
            function (User $user, Company $company, Project $project, Task $task) use ($tagIds) {
                $tags = $this->tagRepository->findBy(['project' => $project, 'relativeId' => $tagIds]);
                foreach ($tags as $tag) {
                    $task->addTag($tag);
                }
                $result = $this->taskRepository->update($task);
                if ($result) {
                    return $this->createDatabaseErrorResponse($result);
                }
                return $this->createSuccessfulResponse($this->getSerializer()->taskFull($task));
            }
        );
    }

    protected function validateCompanyProjectTask(?string $token, int $companyId, string $projectCode, int $taskId, callable $onSuccess): array
    {
        return $this->validateCompanyProject($token, $companyId, $projectCode,
            function (User $user, Company $company, Project $project) use ($taskId, $onSuccess) {
                $task = $this->taskRepository->findOneBy(['relativeId' => $taskId, 'project' => $project]);

                if ($task === null) {
                    return $this->createEntityNotFoundResponse('Task');
                }

                return $onSuccess($user, $company, $project, $task);
            }
        );
    }

    public function remove(string $token, int $companyId, string $projectCode, int $taskId, array $tagIds): array
    {
        return $this->validateCompanyProjectTask($token, $companyId, $projectCode, $taskId,
            function (User $user, Company $company, Project $project, Task $task) use ($tagIds) {
                $preparedTags = $task->getTags()->filter(static function (Tag $tag) use ($tagIds) {
                    return in_array($tag->getRelativeId(), $tagIds, true);
                });
                foreach ($preparedTags as $preparedTag) {
                    $task->removeTag($preparedTag);
                }
                $this->taskRepository->update($task);
                return $this->createSuccessfulResponse($this->getSerializer()->taskFull($task));
            }
        );
    }

}