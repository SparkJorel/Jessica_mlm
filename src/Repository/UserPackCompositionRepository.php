<?php

namespace App\Repository;

use App\Entity\UserPackComposition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserPackComposition|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPackComposition|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPackComposition[]    findAll()
 * @method UserPackComposition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserPackCompositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPackComposition::class);
    }

    // /**
    //  * @return UserPackComposition[] Returns an array of UserPackComposition objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserPackComposition
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
