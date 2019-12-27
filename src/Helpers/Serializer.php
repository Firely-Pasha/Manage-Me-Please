<?php


namespace App\Helpers;


use App\Entity\Company;
use App\Entity\Project;
use App\Entity\SimpleToken;
use App\Entity\Task;
use App\Entity\TaskList;
use App\Entity\User;
use Doctrine\ORM\PersistentCollection;

class Serializer
{
    public function createSimpleResponse($data): array
    {
        return [
            'data' => $data
        ];
    }

    public function accessToken(string $token): array
    {
        return [
            'token' => $token
        ];
    }

    public function userShort(?User $user, bool $wrapToObject = false): ?array
    {
        if ($user === null) {
            return null;
        }
        $data = [];
        $data['id'] = $user->getId();
        $data['login'] = $user->getLogin();
        return $this->wrapOrNot('user', $wrapToObject, $data);
    }

    public function userFull(User $user, bool $wrapToObject = false): array
    {
        if ($user === null) {
            return null;
        }
        $data = $this->userShort($user, false);
        $data['name'] = $user->getName();
        $data['surname'] = $user->getSurname();
        $data['createDate'] = DateHelper::datetimeToString($user->getCreateDate());
        return $this->wrapOrNot('user', $wrapToObject, $data);
    }

    public function userCollection(PersistentCollection $collection, bool $wrapToObject = false): array
    {
        return $this->wrapOrNot('users', $wrapToObject,
            $this->getCollection($collection, function (User $user) {
                return $this->userShort($user, false);
            })
        );
    }

    public function registrationSuccess(User $user, SimpleToken $simpleToken): array
    {
        return [
            'id' => $user->getId(),
            'token' => $simpleToken->getId(),
            'expiresIn' => DateHelper::datetimeToString($simpleToken->getExpiresIn()),
        ];
    }

    public function companyShort(Company $company, bool $wrapToObject = false): array
    {
        $data = [];
        $data['id'] = $company->getId();
        $data['name'] = $company->getName();
        return $this->wrapOrNot('company', $wrapToObject, $data);
    }

    public function companyFull(Company $company, bool $wrapToObject = false): array
    {
        $data = $this->companyShort($company, false);
        $data['createdAt'] = DateHelper::datetimeToString($company->getCreateDate());
        return $this->wrapOrNot('company', $wrapToObject, $data);
    }

    public function companyCollection(PersistentCollection $collection, bool $wrapToObject = false): array
    {
        return $this->wrapOrNot('companies', $wrapToObject,
            $this->getCollection($collection, function (Company $company) {
                return $this->companyShort($company, false);
            })
        );
    }

    public function projectShort(Project $project, bool $wrapToObject = false): array
    {
        $data = [];
        $data['code'] = $project->getCode();
        $data['name'] = $project->getName();
        $data['company'] = $this->companyShort($project->getCompany(), false);
        return $this->wrapOrNot('project', $wrapToObject, $data);
    }

    public function projectFull(Project $project, bool $wrapToObject = false): array
    {
        $data = $this->projectShort($project, false);
        $data['taskLists'] = $this->taskListCollection($project->getTaskLists(), false);
        return $this->wrapOrNot('project', $wrapToObject, $data);
    }

    public function projectCollection(PersistentCollection $collection, bool $wrapToObject = false): array
    {
        return $this->wrapOrNot('projects', $wrapToObject,
            $this->getCollection($collection, function (Project $project) {
                return $this->projectShort($project, false);
            })
        );
    }

    public function taskListSort(TaskList $taskList, bool $wrapToObject = false): array
    {
        $data = [];
        $data['id'] = $taskList->getRelativeId();
        $data['name'] = $taskList->getName();
        return $this->wrapOrNot('taskList', $wrapToObject, $data);
    }

    public function taskListFull(TaskList $taskList, bool $wrapToObject = false): array
    {
        $data = $this->taskListSort($taskList, false);
        return $this->wrapOrNot('taskList', $wrapToObject, $data);
    }

    public function taskListCollection(PersistentCollection $collection, bool $wrapToObject = false): array
    {
        return $this->wrapOrNot('taskLists', $wrapToObject,
            $this->getCollection($collection, function (TaskList $taskList) {
                return $this->taskListSort($taskList);
            })
        );
    }

    public function taskShort(Task $task, bool $wrapToObject = false): array
    {
        $data = [];
        $data['id'] = $task->getRelativeId();
        $data['code'] = $this->createTaskCode($task);
        $data['name'] = $task->getName();
        $data['assignedBy'] = $this->userShort($task->getAssignedBy(), false);
        if ($task->getAssignedTo() !== null) {
            $data['assignedTo'] = $this->userShort($task->getAssignedTo(), false);
        }
        if ($task->getTaskList() !== null) {
            $data['taskList'] = $this->taskListSort($task->getTaskList(), false);
        }
        return $this->wrapOrNot('task', $wrapToObject, $data);
    }

    public function taskFull(Task $task, bool $wrapToObject = false): array
    {
        $data = $this->taskShort($task, false);
        $data['project'] = $this->projectShort($task->getProject(), false);
        return $this->wrapOrNot('task', $wrapToObject, $data);
    }

    public function taskCollection(PersistentCollection $taskCollection, bool $wrapToObject = false): array
    {
        return $this->wrapOrNot('taskLists', $wrapToObject,
            $this->getCollection($taskCollection, function (Task $task) {
                return $this->taskShort($task);
            })
        );
    }

    private function wrapOrNot(string $objectName, bool $wrapOrNot, array $data): array
    {
        if ($wrapOrNot) {
            return [
                $objectName => $data
            ];
        }

        return $data;
    }

    private function createTaskCode(Task $task): string
    {
        return $task->getTaskList()->getProject()->getCode() . '-' . $task->getRelativeId();
    }

    private function getCollection(PersistentCollection $collection, callable $itemSerializer): array
    {
        $data = [];
        foreach ($collection as $item) {
            $data[] = $itemSerializer($item);
        }
        return $data;
    }
}