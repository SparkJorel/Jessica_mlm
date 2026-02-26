<?php

namespace App\Repository;

use App\Entity\Cycle;
use App\Entity\Membership;
use App\Entity\MembershipCost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Cache;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MembershipCost|null find($id, $lockMode = null, $lockVersion = null)
 * @method MembershipCost|null findOneBy(array $criteria, array $orderBy = null)
 * @method MembershipCost[]    findAll()
 * @method MembershipCost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MembershipCostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MembershipCost::class);
    }

    public function getMembershipCost(Membership $membership, \DateTime $date)
    {
        $qb = $this->_em->createQueryBuilder();
        return $qb
            ->select('mc', 'm')
            ->from(MembershipCost::class, 'mc')
            ->innerJoin('mc.membership', 'm')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('m', ':membership'),
                    $qb->expr()->lte('p.recordDate', ':date'),
                    $qb->expr()->gte('p.deactivatedDate', ':date')
                )
            )
            ->setParameters(
                [
                    'name' => $membership,
                    'date' => $date->format('Y-m-d')
                ]
            )
            ->getQuery()
            ->setCacheable(true)
            ->setCacheMode(Cache::MODE_NORMAL)
            ->getResult();
    }

    /**
     * @param Membership $membership
     * @param Cycle $cycle
     * @return MembershipCost|null
     */
    public function membershipCost(Membership $membership, Cycle $cycle)
    {
        $qb = $this->_em->createQueryBuilder();

        try {
            return $qb
                ->select('m', 'm_c')
                ->from('App\Entity\MembershipCost', 'm_c')
                ->innerJoin('m_c.membership', 'm')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('m', ':membership'),
                        $qb->expr()->lt('m_c.startedAt', ':endedAt')
                    )
                )
                ->setParameter('membership', $membership)
                ->setParameter('endedAt', $cycle->getEndedAt()->format('Y-m-d H:i:s'))
                ->setMaxResults(1)
                ->orderBy('m_c.startedAt', 'DESC')
                ->addOrderBy('m_c.id', 'DESC')
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }



    // /**
    //  * @return MembershipCost[] Returns an array of MembershipCost objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MembershipCost
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
