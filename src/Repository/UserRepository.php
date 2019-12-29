<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    private $passwordEncoder;

    public function __construct(ManagerRegistry $registry, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($registry, User::class);
        $this->passwordEncoder = $passwordEncoder;
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


    /**
     * @param string $login
     * @return User
     * @throws NonUniqueResultException
     */
    public function findByLogin(string $login): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.login = :qLogin')
            ->setParameter('qLogin', $login)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $name
     * @return User[]
     */
    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.name = :val')
            ->setParameter('val', $name)
            ->getQuery()
            ->getResult();
    }

    public function findByApiToken(string $token): ?User
    {
        return $this->findOneBy(['apiToken' => $token]);
    }

    public function addUser(User $user): bool
    {
        try {
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush($user);
            return true;
        } catch (ORMException | UniqueConstraintViolationException | OptimisticLockException $e) {
            return false;
        }
    }

    public function update(User $user): bool
    {
        try {
            $this->getEntityManager()->flush($user);
            return true;
        } catch (ORMException | UniqueConstraintViolationException | OptimisticLockException $e) {
            return false;
        }
    }
}
