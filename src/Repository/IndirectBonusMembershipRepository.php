<?php

namespace App\Repository;

use App\Entity\IndirectBonusMembership;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method IndirectBonusMembership|null find($id, $lockMode = null, $lockVersion = null)
 * @method IndirectBonusMembership|null findOneBy(array $criteria, array $orderBy = null)
 * @method IndirectBonusMembership[]    findAll()
 * @method IndirectBonusMembership[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IndirectBonusMembershipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IndirectBonusMembership::class);
    }

    // /**
    //  * @return IndirectBonusMembership[] Returns an array of IndirectBonusMembership objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IndirectBonusMembership
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
