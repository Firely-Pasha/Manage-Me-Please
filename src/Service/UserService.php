<?php


namespace App\Service;


use App\Entity\Company;
use App\Entity\SimpleToken;
use App\Entity\User;
use App\Forms\UserLogin;
use App\Helpers\Serializer;
use App\Repository\CompanyRepository;
use App\Repository\ProjectRepository;
use App\Repository\SimpleTokenRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Tests\Encoder\PasswordEncoder;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService extends BaseService
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(Serializer $serializer, ValidatorInterface $validator,
                                SimpleTokenRepository $simpleTokenRepository, CompanyRepository $companyRepository,
                                ProjectRepository $projectRepository, UserRepository $userRepository,
                                UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($serializer, $validator, $simpleTokenRepository, $companyRepository, $projectRepository, $userRepository);
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getUser(?string $token, ?int $userId): array
    {
        if ($userId !== null) {
            $user = $this->getUserRepository()->find($userId);
            if ($user === null) {
                return $this->createEntityNotFoundResponse('User');
            }
            return $this->createResponse($this->getSerializer()->userFull($user), self::RESPONSE_CODE_SUCCESS);
        }

        $currentUser = $token ? $this->getSimpleTokenRepository()->find($token)->getUser() : null;
        if ($currentUser !== null) {
            return $this->createResponse($this->getSerializer()->userFull($currentUser), self::RESPONSE_CODE_SUCCESS);
        }

        return $this->createEntityNotFoundResponse('User');
    }

    public function register($login, $email, $password, $name, $surname): array
    {
        $user = new User();
        $user->setLogin($login)
            ->setEmail($email)
            ->setName($name)
            ->setSurname($surname);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));

        $errors = $this->getValidator()->validate($user);
        if ($errors->count()) {
            return $this->createValidationListErrorResponse($errors);
        }

        if (!$this->getUserRepository()->addUser($user)) {
            return $this->createResponse(["Couldn't create user"], self::RESPONSE_CODE_FAIL_VALIDATION);
        }

        $simpleToken = (new SimpleToken())
            ->genId()
            ->setUser($user);

        $this->getSimpleTokenRepository()->createToken($simpleToken);

        return $this->createResponse($this->getSerializer()->registrationSuccess($user, $simpleToken), self::RESPONSE_CODE_SUCCESS);
    }

    public function login($login, $password)
    {
        $userLoginParams = new UserLogin();
        $userLoginParams->setLogin($login)
            ->setPassword($password);

        $errors = $this->getValidator()->validate($userLoginParams);
        if ($errors->count()) {
            return $this->createValidationListErrorResponse($errors);
        }

        $user = $this->getUserRepository()->findByLogin($userLoginParams->getLogin());
        if ($user === null) {
            return $this->createEntityNotFoundResponse(['User with this login doesn\'t exist']);
        }

        $isPasswordValid = $this->passwordEncoder->isPasswordValid($user, $userLoginParams->getPassword());
        if (!$isPasswordValid) {
            return $this->createValidationErrorResponse(['Password is invalid']);
        }

        $simpleToken = (new SimpleToken())
            ->genId()
            ->setUser($user);

        $this->getSimpleTokenRepository()->createToken($simpleToken);

        return $this->createResponse($this->getSerializer()->accessToken($simpleToken->getId()), self::RESPONSE_CODE_SUCCESS);
    }

    public function getUserTasks(string $token, ?int $userId, ?int $companyId): array
    {
        $currentUser = $this->getSimpleTokenRepository()->find($token)->getUser();

        if ($userId !== null) {
            $user = $this->getUserRepository()->find($userId);
            if ($user === null) {
                return $this->createEntityNotFoundResponse('User');
            }
            $currentUser = $user;
        }

        if ($companyId !== null) {
            return $this->validateCompany($token, $companyId,
                function (User $user, Company $company) use ($currentUser) {
                    return $this->createSuccessfulResponse($this->getSerializer()->taskCollection($currentUser->getTasksByCompanyId($company->getId())));
                }
            );
        }

        return $this->createSuccessfulResponse($this->getSerializer()->taskCollection($currentUser->getTasks()));
    }

    public function getUserCompanies(string $token, ?int $userId): ?array
    {
        $currentUser = $this->getSimpleTokenRepository()->find($token)->getUser();

        if ($userId !== null) {
            $user = $this->getUserRepository()->find($userId);
            if ($user === null) {
                return $this->createEntityNotFoundResponse('User');
            }

            return $this->createSuccessfulResponse($this->getSerializer()->companyCollection($user->getCompanies()));
        }

        return $this->createSuccessfulResponse($this->getSerializer()->companyCollection($currentUser->getCompanies()));
    }

    public function getUserProjects(string $token, ?int $userId, ?int $companyId): array
    {
        $currentUser = $this->getSimpleTokenRepository()->find($token)->getUser();

        if ($userId !== null) {
            $user = $this->getUserRepository()->find($userId);
            if ($user === null) {
                return $this->createEntityNotFoundResponse('User');
            }
            $currentUser = $user;
        }

        if ($companyId !== null) {
            return $this->validateCompany($token, $companyId,
                function (User $user, Company $company) use ($currentUser) {
                    return $this->createSuccessfulResponse($this->getSerializer()->projectCollection($currentUser->getProjectsByCompanyId($company->getId())));
                }
            );
        }

        return $this->createSuccessfulResponse($this->getSerializer()->projectCollection($currentUser->getProjects()));
    }

    public function getUserWorkLogs(
        string $token, ?int $userId, ?int $companyId
    )
    {
        $currentUser = $this->getSimpleTokenRepository()->find($token)->getUser();

        if ($userId !== null) {
            $user = $this->getUserRepository()->find($userId);
            if ($user === null) {
                return $this->createEntityNotFoundResponse('User');
            }
            $currentUser = $user;
        }

        if ($companyId !== null) {
            return $this->validateCompany($token, $companyId,
                function (User $user, Company $company) use ($currentUser) {
                    return $this->createSuccessfulResponse($this->getSerializer()->workLogCollection($currentUser->getWorkLogsByCompanyId($company->getId())));
                }
            );
        }

        return $this->createSuccessfulResponse($this->getSerializer()->projectCollection($currentUser->getWorkLogs()));
    }
}