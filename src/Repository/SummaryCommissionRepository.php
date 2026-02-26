<?php

namespace App\Repository;

use App\Entity\Cycle;
use App\Entity\SummaryCommission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SummaryCommission|null find($id, $lockMode = null, $lockVersion = null)
 * @method SummaryCommission|null findOneBy(array $criteria, array $orderBy = null)
 * @method SummaryCommission[]    findAll()
 * @method SummaryCommission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SummaryCommissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SummaryCommission::class);
    }

    /**
     * @param array $users_id
     * @param Cycle $cycle
     * @return SummaryCommission[]|null
     */
    public function getReportCycleClosed(array $users_id, Cycle $cycle): ?array
    {
        $qb = $this->createQueryBuilder('sc');

        return
                $qb
                    ->addSelect('u')
                    ->innerJoin('sc.user', 'u')
                    ->andWhere(
                        $qb->expr()->andX(
                            $qb->expr()->in('u.id', ':users'),
                            $qb->expr()->eq('sc.startedAt', ':startedAt'),
                            $qb->expr()->eq('sc.endedAt', ':endedAt')
                        )
                    )
                    ->setParameter('users', $users_id)
                    ->setParameter('startedAt', $cycle->getStartedAt())
                    ->setParameter('endedAt', $cycle->getEndedAt())
                    ->orderBy('u.fullname', 'ASC')
                    ->getQuery()
                    ->getResult()
        ;
    }

    // /**
    //  * @return SummaryCommission[] Returns an array of SummaryCommission objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SummaryCommission
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
