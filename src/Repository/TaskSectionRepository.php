<?php

namespace App\Repository;

use App\Entity\TaskSection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TaskSection|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskSection|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskSection[]    findAll()
 * @method TaskSection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskSectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskSection::class);
    }

    // /**
    //  * @return TaskSection[] Returns an array of TaskSection objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TaskSection
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
