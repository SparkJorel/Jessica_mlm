<?php

namespace App\Repository;

use App\Entity\PackPromo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PackPromo|null find($id, $lockMode = null, $lockVersion = null)
 * @method PackPromo|null findOneBy(array $criteria, array $orderBy = null)
 * @method PackPromo[]    findAll()
 * @method PackPromo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PackPromoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PackPromo::class);
    }

    // /**
    //  * @return PackPromo[] Returns an array of PackPromo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PackPromo
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
