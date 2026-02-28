<?php

namespace App\Repository;

use App\Entity\Cycle;
use App\Entity\Grade;
use App\Entity\GradeBG;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method GradeBG|null find($id, $lockMode = null, $lockVersion = null)
 * @method GradeBG|null findOneBy(array $criteria, array $orderBy = null)
 * @method GradeBG[]    findAll()
 * @method GradeBG[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GradeBGRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GradeBG::class);
    }

    /**
     * @param Grade $grade
     * @param string $levelName
     * @return GradeBG|null
     */
    public function getLastBGofGrade(Grade $grade, string $levelName)
    {
        $qb = $this->_em->createQueryBuilder();
        try {
            return $qb
                ->select('gbg', 'g')
                ->from(GradeBG::class, 'gbg')
                ->innerJoin('gbg.grade', 'g')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('g', ':grade'),
                        $qb->expr()->eq('gbg.name', ':levelName'),
                        $qb->expr()->eq('gbg.status', ':status')
                    )
                )
                ->setParameters(
                    [
                        'grade' => $grade,
                        'levelName' => $levelName,
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
     * @return GradeBG[]|null
     */
    public function getBGofGradeMatchingCycle(Grade $grade, Cycle $cycle)
    {
        $qb = $this->_em->createQueryBuilder();
        return $qb
                        ->select('gbg', 'g')
                        ->from(GradeBG::class, 'gbg')
                        ->innerJoin('gbg.grade', 'g')
                        ->where(
                            $qb->expr()->andX(
                                $qb->expr()->eq('g', ':grade'),
                                $qb->expr()->lt('gbg.startedAt', ':endedAt')
                            )
                        )
                        ->setParameters(
                            [
                                'grade' => $grade,
                                'endedAt' => $cycle->getEndedAt()->format('Y-m-d H:i:s')
                            ]
                        )
                        ->orderBy('gbg.startedAt', 'DESC')
                        ->getQuery()
                        ->getResult();
    }

    /**
     * @return GradeBG[]|null
     */
    public function getAllActivatedBG()
    {
        $qb = $this->_em->createQueryBuilder();

        return $qb
            ->select('gbg', 'l', 'g')
            ->from(GradeBG::class, 'gbg')
            ->innerJoin('gbg.lvl', 'l')
            ->innerJoin('gbg.grade', 'g')
            ->where(
                $qb->expr()->eq('gbg.status', ':status')
            )
            ->setParameter('status', true)
            ->getQuery()
            ->getResult();
    }


    // /**
    //  * @return GradeBG[] Returns an array of GradeBG objects
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
    public function findOneBySomeField($value): ?GradeBG
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
