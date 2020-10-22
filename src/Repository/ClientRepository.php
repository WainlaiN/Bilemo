<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use League\OAuth2\Client\Provider\GithubResourceOwner;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function findOrCreateFromGithubOauth(GithubResourceOwner $owner): Client
    {
        $client = $this->createQueryBuilder('u')
            ->where('u.githubId = :githubId')
            ->setParameters(
                [
                    'githubId' => $owner->getId(),
                ]
            )
            ->getQuery()
            ->getOneOrNullResult();

        if ($client) {
            return $client;
        }

        $client = New Client();
        $client->setGithubId($owner->getId());
        $client->setEmail("test@test.com");
        $client->setRoles();

        $em = $this->getEntityManager();
        $em->persist($client);
        $em->flush();

        return $client;
    }

}
