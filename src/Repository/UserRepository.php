<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByClient($client)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.client = :val')
            ->setParameter('val', $client)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //query for users pagination
    public function findPageByClient($client)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.client = :val')
            ->setParameter('val', $client)
            ->orderBy('u.id', 'ASC')
            ->getQuery();
    }


}

