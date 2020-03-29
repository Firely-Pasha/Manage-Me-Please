<?php


namespace App\Service;


use App\Entity\Company;
use App\Entity\Project;
use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\User;
use App\Entity\WorkLog;
use App\Exceptions\AccessException;
use App\Exceptions\DatabaseException;
use App\Exceptions\EntityNotFoundException;
use App\Helpers\Serializer;
use App\Repository\CompanyRepository;
use App\Repository\ProjectRepository;
use App\Repository\SimpleTokenRepository;
use App\Repository\TagRepository;
use App\Repository\TaskListRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Repository\WorkLogRepository;
use DateTime;
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

    /**
     * @var WorkLogRepository
     */
    private $workLogRepository;

    public function __construct(Serializer $serializer, ValidatorInterface $validator,
                                ProjectRepository $projectRepository, SimpleTokenRepository $simpleTokenRepository,
                                CompanyRepository $companyRepository, TaskListRepository $taskListRepository,
                                UserRepository $userRepository, TaskRepository $taskRepository, TagRepository $tagRepository, WorkLogRepository $workLogRepository)
    {
        parent::__construct($serializer, $validator, $simpleTokenRepository, $companyRepository, $projectRepository, $userRepository);

        $this->taskListRepository = $taskListRepository;
        $this->taskRepository = $taskRepository;
        $this->tagRepository = $tagRepository;
        $this->workLogRepository = $workLogRepository;
    }

    /**
     * @param string $token
     * @param int $companyId
     * @param string $projectCode
     * @param string $taskId
     * @return array
     * @throws EntityNotFoundException
     */
    public function getTask(string $token, int $companyId, string $projectCode, string $taskId): array
    {
        return $this->validateCompanyProject($token, $companyId, $projectCode,
            function (User $user, Company $company, Project $project) use ($taskId) {
                $task = $this->taskRepository->findOneBy([
                    'project' => $project,
                    'relativeId' => $taskId,
                    'deleted' => false
                ]);
                if ($task === null) {
                    throw new EntityNotFoundException('Task');
                }

                return $this->getSerializer()->taskFull($task, false);
            }
        );
    }

    /**
     * @param string $token
     * @param int $companyId
     * @param string $projectCode
     * @param string $name
     * @param int|null $taskListId
     * @param int|null $assignedToId
     * @param string|null $description
     * @param array|null $tagIds
     * @return array
     * @throws EntityNotFoundException
     */
    public function createTask(
        /* REQUIRED: */ string $token, int $companyId, string $projectCode, string $name,
        /* OPTIONAL: */ ?int $taskListId, ?int $assignedToId, ?string $description, ?array $tagIds
    ): array
    {
        return $this->validateCompanyProject($token, $companyId, $projectCode,
            function (User $user, Company $company, Project $project) use ($name, $assignedToId, $taskListId, $description, $tagIds) {
                // ASSIGMENT
                $assignedTo = $assignedToId ? $this->getUserRepository()->find($assignedToId) : null;
                if ($assignedToId !== null) {
                    if ($assignedTo === null || !$company->getEmployees()->contains($assignedTo)) {
                        throw new EntityNotFoundException('AssignedTo');
                    }
                    if (!$project->getUsers()->contains($assignedTo)) {
                        throw new AccessException('Assigned user is not in this project');
                    }
                }

                // TASKLIST
                $taskList = $taskListId ? $this->taskListRepository->findOneByRelativeId($project, $taskListId) : null;
                if (empty($taskListId) && $project->getTaskLists()->count() != 0 || !empty($taskListId) && $taskList === null) {
                    throw new EntityNotFoundException('TaskList');
                }

                // TAGS
                $tags = $this->tagRepository->findBy(['project' => $project, 'relativeId' => $tagIds]);

                // CREATION
                $newTask = Task::create($project, $name, $user, $taskList, $assignedTo, $description, $tags);
                return $this->addEntity($this->taskRepository, $newTask,
                    function (Task $task) {
                        return $this->getSerializer()->taskShort($task);
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
     * @throws EntityNotFoundException
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
                    throw new DatabaseException($result);
                }
                return $this->getSerializer()->taskFull($task);
            }
        );
    }

    /**
     * @param string $token
     * @param int $companyId
     * @param string $projectCode
     * @param int $taskId
     * @param array $tagIds
     * @return array
     * @throws EntityNotFoundException
     */
    public function removeTags(string $token, int $companyId, string $projectCode, int $taskId, array $tagIds): array
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
                return $this->getSerializer()->taskFull($task);
            }
        );
    }

    /**
     * @param string $token
     * @param int $companyId
     * @param string $projectCode
     * @param int $taskId
     * @param int|null $userId
     * @param int|null $workLogId
     * @param int|null $startTime
     * @param int|null $endTime
     * @param string|null $comment
     * @return array
     * @throws EntityNotFoundException
     */
    public function addWorkLog(
        string $token, int $companyId, string $projectCode, int $taskId,
        ?int $userId, ?int $workLogId, ?int $startTime, ?int $endTime, ?string $comment
    ): array
    {
        return $this->validateCompanyProjectTask($token, $companyId, $projectCode, $taskId,
            function (User $user, Company $company, Project $project, Task $task) use ($userId, $workLogId, $startTime, $endTime, $comment) {
                if (!empty($userId)) {
                    $user = $this->getUserRepository()->find($userId);
                }
                if (!$project->doesUserBelong($user)) {
                    throw new EntityNotFoundException('Project');
                }
                if (!empty($workLogId)) {
                    $workLog = $this->workLogRepository->findOneBy([
                        'task' => $task,
                        'relative_id' => $workLogId
                    ]);
                    if ($workLog == null) {
                        throw new EntityNotFoundException('Work Log');
                    }
                    if (!empty($startTime)) {
                        $workLog->setTimeStart($startTime);
                    }
                    $workLog->setTimeEnd($endTime ?? (new DateTime())->getTimestamp());
                    if (!empty($comment)) {
                        $workLog->setComment($comment);
                    }
                    $this->workLogRepository->update($workLog);
                } else {
                    $workLog = WorkLog::create($task, $user, $startTime, $endTime, $comment);
                    if (!$this->workLogRepository->add($workLog)) {
                        throw new DatabaseException('Couldn\'t add work log');
                    }
                }
                return $this->getSerializer()->workLogShort($workLog);
            }
        );
    }

    /**
     * @param string|null $token
     * @param int $companyId
     * @param string $projectCode
     * @param int $taskId
     * @param callable $onSuccess
     * @return array
     * @throws EntityNotFoundException
     */
    protected function validateCompanyProjectTask(?string $token, int $companyId, string $projectCode, int $taskId, callable $onSuccess): array
    {
        return $this->validateCompanyProject($token, $companyId, $projectCode,
            function (User $user, Company $company, Project $project) use ($taskId, $onSuccess) {
                $task = $this->taskRepository->findOneBy([
                    'relativeId' => $taskId,
                    'project' => $project
                ]);

                if ($task === null) {
                    throw new EntityNotFoundException('Task');
                }

                return $onSuccess($user, $company, $project, $task);
            }
        );
    }

}