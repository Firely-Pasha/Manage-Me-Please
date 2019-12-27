<?php

namespace App\Repository;

use App\Entity\SimpleToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * @method SimpleToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method SimpleToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method SimpleToken[]    findAll()
 * @method SimpleToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SimpleTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SimpleToken::class);
    }

    // /**
    //  * @return SimpleToken[] Returns an array of SimpleToken objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SimpleToken
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function createToken(SimpleToken $simpleToken): ?bool
    {
        try {
            $this->getEntityManager()->persist($simpleToken);
            $this->getEntityManager()->flush($simpleToken);
            return true;
        } catch (ORMException | UniqueConstraintViolationException | OptimisticLockException $e) {
            return false;
        }

    }
}
