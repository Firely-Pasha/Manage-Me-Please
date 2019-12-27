<?php

namespace App\Repository;

use App\Entity\db\OAuthRefreshToken;
use App\Entity\db\OAuthToken;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method OAuthToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method OAuthToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method OAuthToken[]    findAll()
 * @method OAuthToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OAuthTokenRepository extends ServiceEntityRepository
{
    /**
     * @var UserRepository $userRepository;
     */
    private $userRepository;

    public function __construct(ManagerRegistry $registry, UserRepository $userRepository)
    {
        parent::__construct($registry, OAuthToken::class);
    }

    // /**
    //  * @return OAuthToken[] Returns an array of OAuthToken objects
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
    public function findOneBySomeField($value): ?OAuthToken
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Revoke an access token.
     *
     * @param string $tokenId
     */
    public function revokeAccessToken($tokenId)
    {
        $accessToken = $this->find($tokenId);
        if (empty($accessToken)) {
            return null;
        }
        $accessToken->setRevoked(true);
        $this->getEntityManager()->flush($accessToken);
    }

    /**
     * Check if the access token has been revoked.
     *
     * @param string $tokenId
     *
     * @return bool Return true if this token has been revoked
     */
    public function isAccessTokenRevoked($tokenId)
    {
        $accessToken = $this->find($tokenId);
        if (empty($accessToken)) {
            return true;
        }
        return $accessToken->isRevoked();
    }

}
