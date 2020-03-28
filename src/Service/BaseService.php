<?php


namespace App\Service;


use App\Entity\Company;
use App\Entity\User;
use App\Helpers\Serializer;
use App\Repository\CompanyRepository;
use App\Repository\ProjectRepository;
use App\Repository\SimpleTokenRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseService
{

    public const RESPONSE_CODE_SUCCESS = 1;
    public const RESPONSE_CODE_FAIL_JSON = 16;
    public const RESPONSE_CODE_FAIL_DATABASE = 17;
    public const RESPONSE_CODE_FAIL_ENTITY_NOT_FOUND = 18;
    public const RESPONSE_CODE_FAIL_VALIDATION = 19;
    public const RESPONSE_CODE_FAIL_AUTHORIZATION = 20;
    public const RESPONSE_CODE_FAIL_AUTHORIZATION_TOKEN_EXPIRED = 21;
    public const RESPONSE_CODE_FAIL_AUTHORIZATION_TOKEN_REVOKED = 22;
    public const RESPONSE_CODE_FAIL_PERMISSION_DENIED = 23;
    public const RESPONSE_CODE_FAIL_ALREADY_EXISTS = 24;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var SimpleTokenRepository
     */
    private $simpleTokenRepository;

    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(Serializer $serializer,
                                ValidatorInterface $validator,
                                SimpleTokenRepository $simpleTokenRepository,
                                CompanyRepository $companyRepository,
                                ProjectRepository $projectRepository,
                                UserRepository $userRepository)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->simpleTokenRepository = $simpleTokenRepository;
        $this->companyRepository = $companyRepository;
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;

    }

    /**
     * @return Serializer
     */
    protected function getSerializer(): Serializer
    {
        return $this->serializer;
    }

    /**
     * @return ValidatorInterface
     */
    protected function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }

    /**
     * @return SimpleTokenRepository
     */
    public function getSimpleTokenRepository(): SimpleTokenRepository
    {
        return $this->simpleTokenRepository;
    }

    /**
     * @return CompanyRepository
     */
    public function getCompanyRepository(): CompanyRepository
    {
        return $this->companyRepository;
    }

    /**
     * @return ProjectRepository
     */
    public function getProjectRepository(): ProjectRepository
    {
        return $this->projectRepository;
    }

    /**
     * @return UserRepository
     */
    public function getUserRepository(): UserRepository
    {
        return $this->userRepository;
    }

    /**
     * @param array $data
     * @param int $code
     * @return array
     */
    public function createResponse($data, int $code): array
    {
        if ($code < 16) {
            return [
                'status' => $code,
                'data' => $data
            ];
        }

        return [
            'status' => $code,
            'errors' => $data
        ];
    }

    public function createSuccessfulResponse(array $data): array
    {
        return $this->createResponse($data, self::RESPONSE_CODE_SUCCESS);
    }

    public function createJsonErrorResponse(): array
    {
        return $this->createResponse(['Invalid JSON format'], self::RESPONSE_CODE_FAIL_JSON);
    }

    protected function createValidationListErrorResponse(ConstraintViolationListInterface $errors): array
    {
        $errorList = [];
        foreach ($errors as $error) {
            $errorList[] = $error->getMessage();
        }
        return $this->createResponse($errorList, self::RESPONSE_CODE_FAIL_VALIDATION);
    }

    /**
     * @param array $errors
     * @return string[]
     */
    protected function createValidationErrorResponse(array $errors): array
    {
        $errorList = [];
        foreach ($errors as $error) {
            $errorList[] = $error;
        }
        return $this->createResponse($errorList, self::RESPONSE_CODE_FAIL_VALIDATION);
    }

    /**
     * @param string|array $entity
     * @return array
     */
    protected function createEntityNotFoundResponse($entity): array
    {
        if (is_array($entity)) {
            return $this->createResponse($entity, self::RESPONSE_CODE_FAIL_ENTITY_NOT_FOUND);
        }
        return $this->createResponse([$entity . ' not found'], self::RESPONSE_CODE_FAIL_ENTITY_NOT_FOUND);
    }

    protected function createEntityAlreadyExistsResponse(string $message): array
    {
        return $this->createResponse([$message . ' already exists'], self::RESPONSE_CODE_FAIL_ALREADY_EXISTS);
    }

    protected function createDatabaseErrorResponse(string $message): array
    {
        return $this->createResponse([$message], self::RESPONSE_CODE_FAIL_DATABASE);
    }


    protected function validateCompany(?string $token, int $companyId, callable $onSuccess): array
    {
        $user = $token ? $this->getSimpleTokenRepository()->find($token)->getUser() : null;

        $company = $this->getCompanyRepository()->find($companyId);
        if ($company === null || ($token !== null && !$company->getEmployees()->contains($user))) {
            return $this->createEntityNotFoundResponse('Company');
        }

        return $onSuccess($user, $company);
    }

    protected function validateCompanyProject(?string $token, int $companyId, string $projectCode, callable $onSuccess): array
    {
        return $this->validateCompany($token, $companyId,
            function (User $user, Company $company) use ($projectCode, $onSuccess) {
                $project = $this->getProjectRepository()->findOneBy(['company' => $company, 'code' => $projectCode]);

                if ($project === null) {
                    return $this->createEntityNotFoundResponse('Project');
                }

                if (!$project->getUsers()->contains($user)) {
                    return $this->createResponse(['You have no access to this project'], self::RESPONSE_CODE_FAIL_PERMISSION_DENIED);
                }

                return $onSuccess($user, $company, $project);
            }
        );
    }

    protected function addEntity(ServiceEntityRepository $repository, $entity, callable $onSuccess): ?array
    {
        $errors = $this->getValidator()->validate($entity);
        if ($errors->count()) {
            return $this->createValidationListErrorResponse($errors);
        }

        if (!$repository->add($entity)) {
            return $this->createResponse(["Couldn't create entity"], self::RESPONSE_CODE_FAIL_DATABASE);
        }

        return $onSuccess($entity);
    }
}