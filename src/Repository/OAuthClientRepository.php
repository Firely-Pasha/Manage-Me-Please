<?php

namespace App\Repository;

use App\Entity\db\OAuthClient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method OAuthClient|null find($id, $lockMode = null, $lockVersion = null)
 * @method OAuthClient|null findOneBy(array $criteria, array $orderBy = null)
 * @method OAuthClient[]    findAll()
 * @method OAuthClient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OAuthClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OAuthClient::class);
    }

    // /**
    //  * @return OAuthClient[] Returns an array of OAuthClient objects
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
    public function findOneBySomeField($value): ?OAuthClient
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
     * Get a client.
     *
     * @param string $clientIdentifier The client's identifier
     *
     * @return ClientEntityInterface|null
     */
    public function getClientEntity($clientIdentifier): ClientEntityInterface
    {
        /**
         * @todo Make separate method for this query
         */
        $appClient = $this->find([
            'active' => true,
            'id' => $clientIdentifier
        ]);
        if (empty($appClient)) {
            return null;
        }

        return $appClient;
    }

    /**
     * Validate a client's secret.
     *
     * @param string $clientIdentifier The client's identifier
     * @param null|string $clientSecret The client's secret (if sent)
     * @param null|string $grantType The type of grant the client is using (if sent)
     *
     * @return bool
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType)
    {
        $appClient = $this->find([
            'active' => true,
            'id' => $clientIdentifier
        ]);
        return hash_equals($appClient->getSecret(), (string)$clientSecret);
    }
}
