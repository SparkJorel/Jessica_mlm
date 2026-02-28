<?php

namespace App\Repository;

use App\Entity\LevelBonusGenerationnel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LevelBonusGenerationnel|null find($id, $lockMode = null, $lockVersion = null)
 * @method LevelBonusGenerationnel|null findOneBy(array $criteria, array $orderBy = null)
 * @method LevelBonusGenerationnel[]    findAll()
 * @method LevelBonusGenerationnel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LevelBonusGenerationnelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LevelBonusGenerationnel::class);
    }

    // /**
    //  * @return LevelBonusGenerationnel[] Returns an array of LevelBonusGenerationnel objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LevelBonusGenerationnel
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
