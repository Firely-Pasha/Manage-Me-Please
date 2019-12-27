<?php

namespace App\Repository;

use App\Entity\db\OAuthRefreshToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * @method OAuthRefreshToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method OAuthRefreshToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method OAuthRefreshToken[]    findAll()
 * @method OAuthRefreshToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OAuthRefreshTokenRepository extends ServiceEntityRepository
{
    /**
     * @var OAuthTokenRepository
     */
    private $accessTokenRepository;

    public function __construct(ManagerRegistry $registry, OAuthTokenRepository $accessTokenRepository)
    {
        parent::__construct($registry, OAuthRefreshToken::class);
        $this->accessTokenRepository = $accessTokenRepository;
    }

    // /**
    //  * @return OAuthRefreshToken[] Returns an array of OAuthRefreshToken objects
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
    public function findOneBySomeField($value): ?OAuthRefreshToken
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
     * {@inheritDoc}
     */
    public function getNewRefreshToken()
    {
        return new OAuthRefreshToken();
    }

    /**
     * {@inheritDoc}
     */
    public function revokeRefreshToken($tokenId)
    {
        $refreshTokenPersistEntity = $this->find($tokenId);
        if (empty($refreshTokenPersistEntity)) {
            return;
        }
        $refreshTokenPersistEntity->revoke();
        $this->getEntityManager()->flush($refreshTokenPersistEntity);
    }

    /**
     * {@inheritDoc}
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        $refreshTokenPersistEntity = $this->find($tokenId);
        if ($refreshTokenPersistEntity === null || $refreshTokenPersistEntity->isRevoked()) {
            return true;
        }
        return $this->accessTokenRepository->isAccessTokenRevoked($refreshTokenPersistEntity->getAccessToken()->getIdentifier());
    }
}
