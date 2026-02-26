<?php

namespace App\Repository;

use App\Entity\TVCPack;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TVCPack|null find($id, $lockMode = null, $lockVersion = null)
 * @method TVCPack|null findOneBy(array $criteria, array $orderBy = null)
 * @method TVCPack[]    findAll()
 * @method TVCPack[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TVCPackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TVCPack::class);
    }

    // /**
    //  * @return TVCPack[] Returns an array of TVCPack objects
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
    public function findOneBySomeField($value): ?TVCPack
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
