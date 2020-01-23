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

class ProjectService extends BaseService
{
    /**
     * @var TaskListRepository
     */
    private $taskListRepository;

    public function __construct(Serializer $serializer, ValidatorInterface $validator, SimpleTokenRepository $simpleTokenRepository, CompanyRepository $companyRepository, ProjectRepository $projectRepository, UserRepository $userRepository, TaskListRepository $taskListRepository)
    {
        parent::__construct($serializer, $validator, $simpleTokenRepository, $companyRepository, $projectRepository, $userRepository);
        $this->taskListRepository = $taskListRepository;
    }

    public function getProject(string $token, int $companyId, string $projectCode): array
    {
        return $this->validateCompanyProject($token, $companyId, $projectCode,
            function (User $user, Company $company, Project $project) {
                return $this->createSuccessfulResponse($this->getSerializer()->projectFull($project));
            }
        );
    }

    public function createProject(string $token, int $companyId, string $code, string $name): array
    {
        return $this->validateCompany($token, $companyId,
            function (User $user, Company $company) use ($code, $name) {
                if (!$company->getEmployees()->contains($user)) {
                    return $this->createResponse(['You cannot create project in this company'], self::RESPONSE_CODE_FAIL_PERMISSION_DENIED);
                }
                if ($this->getProjectRepository()->findOneBy(['company' => $company, 'code' => $code]) !== null) {
                    return $this->createEntityAlreadyExistsResponse('Project with this code');
                }
                $newProject = Project::create($user, $company, $code, $name);
                return $this->addEntity($this->getProjectRepository(), $newProject,
                    function (Project $project) use ($user) {
                        $user->addProject($project);
                        $this->getUserRepository()->update($user);
                        $newDefaultTaskList = TaskList::create('Backlog', $project);
                        return $this->addEntity($this->taskListRepository, $newDefaultTaskList,
                            function (TaskList $taskList) use ($project) {
                                $taskList->setProject($project);
                                return $this->createSuccessfulResponse($this->getSerializer()->projectFull($project));
                            }
                        );
                    }
                );
            }
        );
    }

    public function getUsers(string $token, int $companyId, string $projectCode): array
    {
        return $this->validateCompanyProject($token, $companyId, $projectCode,
            function (User $user, Company $company, Project $project) {
                return $this->createSuccessfulResponse($this->getSerializer()->userCollection($project->getUsers()));
            }
        );
    }

    public function addUser(string $token, int $companyId, string $projectCode, int $userId): array
    {
        return $this->validateCompanyProject($token, $companyId, $projectCode,
            function (User $currentUser, Company $company, Project $project) use ($userId) {
                $user = $this->getUserRepository()->find($userId);
                if ($user === null) {
                    return $this->createEntityNotFoundResponse('User');
                }
                if ($user->getProjects()->contains($project)) {
                    return $this->createEntityAlreadyExistsResponse('User in this project');
                }
                if (!$company->getEmployees()->contains($user)) {
                    return $this->createResponse(['This user is not in this company'], self::RESPONSE_CODE_FAIL_PERMISSION_DENIED);
                }

                $user->addProject($project);
                if (!$this->getUserRepository()->update($user)) {
                    return $this->createDatabaseErrorResponse('Couldn\'t add user to project');
                }

                return $this->createSuccessfulResponse([]);
            }
        );
    }

    public function sortTaskLists(string $token, ?int $companyId, ?string $projectCode, ?array $sort, ?array $deletions): array
    {
        return $this->validateCompanyProject($token, $companyId, $projectCode,
            function (User $currentUser, Company $company, Project $project) use ($sort, $deletions) {
                $taskLists = $project->getTaskLists();

                /**
                 * @var $taskList TaskList
                 */

                if (!empty($sort)) {
                    foreach ($taskLists as $taskList) {
                        $taskList->setSort($sort[$taskList->getRelativeId()] ?? 0);
                        if (!$this->taskListRepository->update($taskList)) {
                            return $this->createDatabaseErrorResponse('Couldn\'t change sort of ' . $taskList->getName() . '.');
                        }
                    }
                }

                if (!empty($deletions)) {
                    foreach ($taskLists as $taskList) {
                        if (in_array($taskList->getRelativeId(), $deletions, true)) {
                            $taskList->setDeleted(true);
                            $this->taskListRepository->update($taskList);
                        }
                    }
                }


                return $this->createSuccessfulResponse([]);
            }
        );
    }
}