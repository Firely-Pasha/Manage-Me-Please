<?php


namespace App\Service;


use App\Entity\Company;
use App\Entity\Project;
use App\Entity\TaskList;
use App\Entity\User;
use App\Helpers\Serializer;
use App\Repository\CompanyRepository;
use App\Repository\ProjectRepository;
use App\Repository\SimpleTokenRepository;
use App\Repository\TaskListRepository;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskListService extends BaseService
{
    /**
     * @var TaskListRepository
     */
    private $taskListRepository;

    public function __construct(Serializer $serializer, ValidatorInterface $validator,
                                SimpleTokenRepository $simpleTokenRepository, CompanyRepository $companyRepository,
                                ProjectRepository $projectRepository, UserRepository $userRepository,
                                TaskListRepository $taskListRepository)
    {
        parent::__construct($serializer, $validator, $simpleTokenRepository, $companyRepository, $projectRepository, $userRepository);

        $this->taskListRepository = $taskListRepository;
    }

    public function getTaskList(string $token, int $companyId, string $projectCode, int $listId): array
    {
        return $this->validateCompanyProject($token, $companyId, $projectCode,
            function (User $user, Company $company, Project $project) use ($listId) {
                $taskList = $this->taskListRepository->findOneBy([
                    'project' => $project,
                    'relativeId' => $listId
                ]);
                if ($taskList === null) {
                    return $this->createEntityNotFoundResponse('Task list');
                }

                return $this->createSuccessfulResponse($this->getSerializer()->taskListFull($taskList));
            }
        );
    }

    public function createTaskList(string $token, int $companyId, string $projectCode, string $name): array
    {
        return $this->validateCompanyProject($token, $companyId, $projectCode,
            function (User $user, Company $company, Project $project) use ($name) {
                $taskList = TaskList::create($name, $project);
                return $this->addEntity($this->taskListRepository, $taskList,
                    function (TaskList $taskList) {
                        return $this->createSuccessfulResponse($this->getSerializer()->taskListFull($taskList));
                    }
                );
            }
        );
    }
}