<?php

namespace App\Repository;

use App\Entity\Grade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Grade|null find($id, $lockMode = null, $lockVersion = null)
 * @method Grade|null findOneBy(array $criteria, array $orderBy = null)
 * @method Grade[]    findAll()
 * @method Grade[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GradeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Grade::class);
    }

    /**
     * @return Grade[]|null
     */
    public function getAll()
    {
        $qb = $this->_em->createQueryBuilder();
        return $qb
                        ->select('g', 'bg')
                        ->from(Grade::class, 'g')
                        ->leftJoin('g.gradeBGs', 'bg')
                        ->orderBy('g.id', 'ASC')
                        ->getQuery()
                        ->setCacheable(true)
                        ->setCacheMode(3)
                        ->getResult();
    }

    public function getGradeMatchingSV(float $binaire)
    {
        $qb = $this->_em->createQueryBuilder();
        return $qb
                        ->select('g')
                        ->from(Grade::class, 'g')
                        ->where(
                            $qb->expr()->lte('g.sv', ':binaire')
                        )
                        ->setParameter('binaire', $binaire)
                        ->setMaxResults(1)
                        ->orderBy('g.sv', 'DESC')
                        ->getQuery()
                        ->getOneOrNullResult();
    }


    // /**
    //  * @return Grade[] Returns an array of Grade objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Grade
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
