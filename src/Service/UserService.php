<?php


namespace App\Service;


use App\Entity\Company;
use App\Entity\SimpleToken;
use App\Entity\User;
use App\Exceptions\AccessException;
use App\Exceptions\DatabaseException;
use App\Exceptions\DataValidationException;
use App\Exceptions\EntityNotFoundException;
use App\Forms\UserLogin;
use App\Helpers\Serializer;
use App\Repository\CompanyRepository;
use App\Repository\ProjectRepository;
use App\Repository\SimpleTokenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Tests\Encoder\PasswordEncoder;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService extends BaseService
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * UserService constructor.
     * @param Serializer $serializer
     * @param ValidatorInterface $validator
     * @param SimpleTokenRepository $simpleTokenRepository
     * @param CompanyRepository $companyRepository
     * @param ProjectRepository $projectRepository
     * @param UserRepository $userRepository
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(Serializer $serializer, ValidatorInterface $validator,
                                SimpleTokenRepository $simpleTokenRepository, CompanyRepository $companyRepository,
                                ProjectRepository $projectRepository, UserRepository $userRepository,
                                UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($serializer, $validator, $simpleTokenRepository, $companyRepository, $projectRepository, $userRepository);
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param string|null $token
     * @param int|null $userId
     * @return array
     * @throws EntityNotFoundException
     */
    public function getUser(?string $token, ?int $userId): array
    {
        if ($userId !== null) {
            $user = $this->getUserRepository()->find($userId);
            if ($user === null) {
                throw new EntityNotFoundException('User');
            }
            return $this->getSerializer()->userFull($user);
        }

        $currentUser = $token ? $this->getSimpleTokenRepository()->find($token)->getUser() : null;
        if ($currentUser !== null) {
            return $this->getSerializer()->userFull($currentUser);
        }

        throw new EntityNotFoundException('User');
    }

    /**
     * @param $login
     * @param $email
     * @param $password
     * @param $name
     * @param $surname
     * @return array
     * @throws DataValidationException
     * @throws DatabaseException
     */
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
            throw new DataValidationException($errors);
        }

        if (!$this->getUserRepository()->addUser($user)) {
            throw new DatabaseException('Couldn\'t create user');
        }

        $simpleToken = (new SimpleToken())
            ->genId()
            ->setUser($user);

        $this->getSimpleTokenRepository()->createToken($simpleToken);

        return $this->getSerializer()->registrationSuccess($user, $simpleToken);
    }

    /**
     * @param $login
     * @param $password
     * @return array|string[]
     * @throws DataValidationException
     * @throws NonUniqueResultException
     * @throws AccessException
     */
    public function login($login, $password)
    {
        $userLoginParams = new UserLogin();
        $userLoginParams->setLogin($login)
            ->setPassword($password);

        $errors = $this->getValidator()->validate($userLoginParams);
        if ($errors->count()) {
            throw new DataValidationException($errors);
        }

        $user = $this->getUserRepository()->findByLogin($userLoginParams->getLogin());
        if ($user === null) {
            return $this->createEntityNotFoundResponse(['User with this login doesn\'t exist']);
        }

        $isPasswordValid = $this->passwordEncoder->isPasswordValid($user, $userLoginParams->getPassword());
        if (!$isPasswordValid) {
            throw new AccessException('Password is invalid');
        }

        $simpleToken = (new SimpleToken())
            ->genId()
            ->setUser($user);

        $this->getSimpleTokenRepository()->createToken($simpleToken);

        return $this->getSerializer()->accessToken($simpleToken->getId());
    }

    /**
     * @param string $token
     * @param int|null $userId
     * @param int|null $companyId
     * @return array
     * @throws EntityNotFoundException
     */
    public function getUserTasks(string $token, ?int $userId, ?int $companyId): array
    {
        $currentUser = $this->getSimpleTokenRepository()->find($token)->getUser();

        if ($userId !== null) {
            $user = $this->getUserRepository()->find($userId);
            if ($user === null) {
                throw new EntityNotFoundException('User');
            }
            $currentUser = $user;
        }

        if ($companyId !== null) {
            return $this->validateCompany($token, $companyId,
                function (User $user, Company $company) use ($currentUser) {
                    return $this->getSerializer()->taskCollection($currentUser->getTasksByCompanyId($company->getId()));
                }
            );
        }

        return $this->getSerializer()->taskCollection($currentUser->getTasks());
    }

    /**
     * @param string $token
     * @param int|null $userId
     * @return array|null
     * @throws EntityNotFoundException
     */
    public function getUserCompanies(string $token, ?int $userId): ?array
    {
        $currentUser = $this->getSimpleTokenRepository()->find($token)->getUser();

        if ($userId !== null) {
            $user = $this->getUserRepository()->find($userId);
            if ($user === null) {
                throw new EntityNotFoundException('User');
            }

            return $this->getSerializer()->companyCollection($user->getCompanies());
        }

        return $this->getSerializer()->companyCollection($currentUser->getCompanies());
    }

    /**
     * @param string $token
     * @param int|null $userId
     * @param int|null $companyId
     * @return array
     * @throws EntityNotFoundException
     */
    public function getUserProjects(string $token, ?int $userId, ?int $companyId): array
    {
        $currentUser = $this->getSimpleTokenRepository()->find($token)->getUser();

        if ($userId !== null) {
            $user = $this->getUserRepository()->find($userId);
            if ($user === null) {
                throw new EntityNotFoundException('User');
            }
            $currentUser = $user;
        }

        if ($companyId !== null) {
            return $this->validateCompany($token, $companyId,
                function (User $user, Company $company) use ($currentUser) {
                    return $this->getSerializer()->projectCollection($currentUser->getProjectsByCompanyId($company->getId()));
                }
            );
        }

        return $this->getSerializer()->projectCollection($currentUser->getProjects());
    }

    /**
     * @param string $token
     * @param int|null $userId
     * @param int|null $companyId
     * @return array
     * @throws EntityNotFoundException
     */
    public function getUserWorkLogs(string $token, ?int $userId, ?int $companyId)
    {
        $currentUser = $this->getSimpleTokenRepository()->find($token)->getUser();

        if ($userId !== null) {
            $user = $this->getUserRepository()->find($userId);
            if ($user === null) {
                throw new EntityNotFoundException('User');
            }
            $currentUser = $user;
        }

        if ($companyId !== null) {
            return $this->validateCompany($token, $companyId,
                function (User $user, Company $company) use ($currentUser) {
                    return $this->getSerializer()->workLogCollection($currentUser->getWorkLogsByCompanyId($company->getId()));
                }
            );
        }

        return $this->getSerializer()->projectCollection($currentUser->getWorkLogs());
    }
}