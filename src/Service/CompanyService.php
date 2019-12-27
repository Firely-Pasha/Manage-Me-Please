<?php


namespace App\Service;


use App\Entity\Company;
use App\Entity\User;

class CompanyService extends BaseService
{
    public function getCompany(string $token, int $companyId): array
    {
        return $this->validateCompany($token, $companyId,
            function (User $user, Company $company) {
                return $this->createResponse($this->getSerializer()->companyFull($company), self::RESPONSE_CODE_SUCCESS);
            }
        );
    }

    public function createCompany(string $token, ?string $name): array
    {
        $currentUser = $this->getSimpleTokenRepository()->find($token)->getUser();

        $newCompany = Company::create($name, $currentUser);
        $errors = $this->getValidator()->validate($newCompany);
        if ($errors->count()) {
            return $this->createValidationListErrorResponse($errors);
        }
        $newCompany->addEmployee($currentUser);

        if (!$this->getCompanyRepository()->add($newCompany)) {
            return $this->createResponse(["Couldn't create company"], self::RESPONSE_CODE_FAIL_VALIDATION);
        }

        return $this->createResponse($this->getSerializer()->companyShort($newCompany), self::RESPONSE_CODE_SUCCESS);
    }

    public function getEmployees(string $token, int $companyId): array
    {
        return $this->validateCompany($token, $companyId,
            function (User $user, Company $company) {
                $employees = $company->getEmployees();

                $employeeList = [];
                foreach ($employees as $employee) {
                    $employeeList[] = $this->getSerializer()->userShort($employee);
                }
                return $this->createResponse($employeeList, self::RESPONSE_CODE_SUCCESS);
            }
        );
    }

    public function addEmployee(string $token, int $companyId, int $userId): array
    {
        $simpleToken = $this->getSimpleTokenRepository()->find($token);
        $currentUser = $simpleToken->getUser();

        $company = $this->getCompanyRepository()->find($companyId);
        if ($company === null) {
            return $this->createEntityNotFoundResponse('Company');
        }

        if ($company->getOwner()->getId() !== $currentUser->getId()) {
            return $this->createResponse(['You must be the company owner to add a new user'], self::RESPONSE_CODE_FAIL_PERMISSION_DENIED);
        }

        $user = $this->getUserRepository()->find($userId);
        if ($user === null) {
            return $this->createEntityNotFoundResponse('User');
        }

        $userExists = $company->getEmployees()->exists(function ($key, User $item) use ($userId) {
            return $item->getId() === $userId;
        });
        if ($userExists) {
            return $this->createResponse(['User already exists in this company'], self::RESPONSE_CODE_FAIL_ALREADY_EXISTS);
        }

        $company->addEmployee($user);
        $this->getCompanyRepository()->update($company);

        return $this->createResponse([], self::RESPONSE_CODE_SUCCESS);
    }

    public function getCompanyProjects(string $token, int $companyId): array
    {
        return $this->validateCompany($token, $companyId,
            function (User $user, Company $company) {
                return $this->getSerializer()->projectCollection($company->getProjects());
            }
        );
    }
}