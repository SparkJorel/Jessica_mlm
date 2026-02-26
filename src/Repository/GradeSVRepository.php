<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\GradeSV;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method GradeSV|null find($id, $lockMode = null, $lockVersion = null)
 * @method GradeSV|null findOneBy(array $criteria, array $orderBy = null)
 * @method GradeSV[]    findAll()
 * @method GradeSV[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GradeSVRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GradeSV::class);
    }

    /**
     * @param Grade $grade
     * @return GradeSV|null
     */
    public function getLastSVofGrade(Grade $grade)
    {
        $qb = $this->_em->createQueryBuilder();
        try {
            return $qb
                ->select('gsv', 'g')
                ->from(GradeSV::class, 'gsv')
                ->innerJoin('gsv.grade', 'g')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('g', ':grade'),
                        $qb->expr()->eq('gsv.status', ':status')
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
     * @param float $binaire
     * @return GradeSV|null
     */
    public function getGradeMatchingSV(float $binaire)
    {
        $qb = $this->_em->createQueryBuilder();
        try {
            return $qb
                ->select('gsv', 'g')
                ->from(GradeSV::class, 'gsv')
                ->innerJoin('gsv.grade', 'g')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->lte('gsv.sv', ':sv'),
                        $qb->expr()->eq('gsv.status', ':status')
                    )
                )
                ->setParameters(
                    [
                        'sv' => $binaire,
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
     * @return GradeSV[]|null
     */
    public function getAllActivatedSV()
    {
        $qb = $this->_em->createQueryBuilder();

        return $qb
            ->select('gsv', 'g')
            ->from(GradeSV::class, 'gsv')
            ->innerJoin('gsv.grade', 'g')
            ->where(
                $qb->expr()->eq('gsv.status', ':status')
            )
            ->setParameter('status', true)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return GradeSV[] Returns an array of GradeSV objects
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
    public function findOneBySomeField($value): ?GradeSV
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
