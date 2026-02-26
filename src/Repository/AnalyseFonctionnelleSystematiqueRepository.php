<?php

namespace App\Repository;

use App\Entity\AnalyseFonctionnelleSystematique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AnalyseFonctionnelleSystematique|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnalyseFonctionnelleSystematique|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnalyseFonctionnelleSystematique[]    findAll()
 * @method AnalyseFonctionnelleSystematique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnalyseFonctionnelleSystematiqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnalyseFonctionnelleSystematique::class);
    }

    // /**
    //  * @return AnalyseFonctionnelleSystematique[] Returns an array of AnalyseFonctionnelleSystematique objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AnalyseFonctionnelleSystematique
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
