<?php

namespace App\Repository;

use App\Entity\Cycle;
use App\Entity\Grade;
use App\Entity\GradeLevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method GradeLevel|null find($id, $lockMode = null, $lockVersion = null)
 * @method GradeLevel|null findOneBy(array $criteria, array $orderBy = null)
 * @method GradeLevel[]    findAll()
 * @method GradeLevel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GradeLevelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GradeLevel::class);
    }

    /**
     * @param Grade $grade
     * @return GradeLevel|null
     */
    public function getLastLeveofGrade(Grade $grade)
    {
        $qb = $this->_em->createQueryBuilder();
        try {
            return $qb
                ->select('gl', 'g')
                ->from(GradeLevel::class, 'gl')
                ->innerJoin('gl.grade', 'g')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('g', ':grade'),
                        $qb->expr()->eq('gl.status', ':status')
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
     * @param Grade $grade
     * @param Cycle $cycle
     * @return GradeLevel|null
     */
    public function getLeveofGradeMatchingCycle(Grade $grade, Cycle $cycle)
    {
        $qb = $this->_em->createQueryBuilder();
        try {
            return $qb
                ->select('gl', 'g')
                ->from(GradeLevel::class, 'gl')
                ->innerJoin('gl.grade', 'g')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('g', ':grade'),
                        $qb->expr()->lt('gl.startedAt', ':endedAt')
                    )
                )
                ->setParameters(
                    [
                        'grade' => $grade,
                        'endedAt' => $cycle->getEndedAt()->format('Y-m-d H:i:s')
                    ]
                )
                ->setMaxResults(1)
                ->orderBy('gl.startedAt', 'DESC')
                ->getQuery()
                ->getOneOrNullResult();
		  
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @return GradeLevel[]|null
     */
    public function getAllActivatedLevel()
    {
        $qb = $this->_em->createQueryBuilder();

        return $qb
                        ->select('glv', 'g')
                        ->from(GradeLevel::class, 'glv')
                        ->innerJoin('glv.grade', 'g')
                        ->where(
                            $qb->expr()->eq('glv.status', ':status')
                        )
                        ->setParameter('status', true)
                        ->getQuery()
                        ->getResult();
    }


    // /**
    //  * @return GradeLevel[] Returns an array of GradeLevel objects
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
    public function findOneBySomeField($value): ?GradeLevel
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
