<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\GradeMaintenance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method GradeMaintenance|null find($id, $lockMode = null, $lockVersion = null)
 * @method GradeMaintenance|null findOneBy(array $criteria, array $orderBy = null)
 * @method GradeMaintenance[]    findAll()
 * @method GradeMaintenance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GradeMaintenanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GradeMaintenance::class);
    }

    /**
     * @param Grade $grade
     * @return GradeMaintenance|null
     */
    public function getLastMaintenaceofGrade(Grade $grade)
    {
        $qb = $this->_em->createQueryBuilder();
        try {
            return $qb
                ->select('gm', 'g')
                ->from(GradeMaintenance::class, 'gm')
                ->innerJoin('gm.grade', 'g')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('g', ':grade'),
                        $qb->expr()->eq('gm.status', ':status')
                    )
                )
                ->setParameters(
                    [
                        'grade' => $grade,
                        'status' => true
                    ]
                )
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
		  
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @return GradeMaintenance[]|null
     */
    public function getAllActivatedMaintenance()
    {
        $qb = $this->_em->createQueryBuilder();

        return $qb
            ->select('gm', 'g')
            ->from(GradeMaintenance::class, 'gm')
            ->innerJoin('gm.grade', 'g')
            ->where(
                $qb->expr()->eq('gm.status', ':status')
            )
            ->setParameter('status', true)
            ->getQuery()
            ->getResult();
    }


    // /**
    //  * @return GradeMaintenance[] Returns an array of GradeMaintenance objects
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
    public function findOneBySomeField($value): ?GradeMaintenance
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
