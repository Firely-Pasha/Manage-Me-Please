<?php

namespace App\Repository;

use App\Entity\Tag;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Log\LoggerInterface;

/**
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    private $logger;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, Tag::class);
        $this->logger = $logger;
    }

    // /**
    //  * @return Tag[] Returns an array of Tag objects
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
    public function findOneBySomeField($value): ?Tag
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function add(Tag $tag): string
    {
        $query = $this->createQueryBuilder('s');
        $query->select('MAX(s.relativeId) AS maxValue');
        $query->andWhere('s.project = :project')->setParameter('project', $tag->getProject());
        try {
            $maxId = $query->getQuery()->getSingleScalarResult();
            $tag->setRelativeId($maxId + 1);
            $this->getEntityManager()->persist($tag);
            $this->getEntityManager()->flush($tag);
            return false;
        } catch (ORMException | UniqueConstraintViolationException | NonUniqueResultException | OptimisticLockException $e) {
            $this->logger->error($e->getMessage());
            return $e->getMessage();
        }
    }

    public function update(Tag $user): string
    {
        try {
            $this->getEntityManager()->flush($user);
            return false;
        } catch (ORMException | UniqueConstraintViolationException | OptimisticLockException $e) {
            $this->logger->error($e->getMessage());
            return $e->getMessage();
        }
    }
}
