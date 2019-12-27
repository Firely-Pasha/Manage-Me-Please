<?php

namespace App\Repository;

use App\Entity\db\OAuthScope;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method OAuthScope|null find($id, $lockMode = null, $lockVersion = null)
 * @method OAuthScope|null findOneBy(array $criteria, array $orderBy = null)
 * @method OAuthScope[]    findAll()
 * @method OAuthScope[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OAuthScopeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OAuthScope::class);
    }

    // /**
    //  * @return OAuthScope[] Returns an array of OAuthScope objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OAuthScope
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @inheritDoc
     */
    public function getScopeEntityByIdentifier($identifier)
    {
        return $this->find($identifier);
    }

    /**
     * @inheritDoc
     */
    public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null)
    {
        /**
         * @var ScopeEntityInterface[] $filteredScopes
         */
        $filteredScopes = [];

        /**
         * @var ScopeEntityInterface $scope
         */
        foreach ($scopes as $scope) {
            if (!empty($this->getScopeEntityByIdentifier($scope))) {
                $filteredScopes[] = $scope;
            };
        }

        return $filteredScopes;
    }
}
