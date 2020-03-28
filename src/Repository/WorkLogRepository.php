<?php

namespace App\Repository;

use App\Entity\WorkLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * @method WorkLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkLog[]    findAll()
 * @method WorkLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkLog::class);
    }

    // /**
    //  * @return WorkLog[] Returns an array of WorkLog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WorkLog
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function add(WorkLog $workLog): bool
    {
        $query = $this->createQueryBuilder('s');
        $query->select('MAX(s.relative_id) AS maxValue');
        $query->andWhere('s.task = :task')->setParameter('task', $workLog->getTask());
        $maxId = $query->getQuery()->getResult()[0]['maxValue'];
        $workLog->setRelativeId($maxId ? $maxId  + 1 : 1);
        try {
            $this->getEntityManager()->persist($workLog);
            $this->getEntityManager()->flush($workLog);
            return true;
        } catch (ORMException | UniqueConstraintViolationException | OptimisticLockException $e) {
            return false;
        }
    }

    public function update(WorkLog $workLog): bool
    {
        try {
            $this->getEntityManager()->flush($workLog);
            return true;
        } catch (ORMException | UniqueConstraintViolationException | OptimisticLockException $e) {
            return false;
        }
    }

}
