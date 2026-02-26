<?php

namespace App\Repository;

use App\Entity\Cycle;
use App\Entity\MembershipSV;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Cache;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MembershipSV|null find($id, $lockMode = null, $lockVersion = null)
 * @method MembershipSV|null findOneBy(array $criteria, array $orderBy = null)
 * @method MembershipSV[]    findAll()
 * @method MembershipSV[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MembershipSVRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MembershipSV::class);
    }

    /**
     * @param $membership
     * @return MembershipSV[]|null
     */
    public function getSVMembership($membership)
    {
        $qb = $this->_em->createQueryBuilder();
        return $qb
                            ->select('m', 'm_sv')
                            ->from('App\Entity\MembershipSV', 'm_sv')
                            ->innerJoin('m_sv.membership', 'm')
                            ->where(
                                $qb->expr()->in('m.id', ':membership')
                            )
                            ->setParameter('membership', $membership)
                            ->getQuery()
                            ->getResult();
    }

    /**
     * @param $membership
     * @param Cycle $cycle
     * @return MembershipSV|null
     */
    public function membershipSV($membership, Cycle $cycle)
    {
        $qb = $this->_em->createQueryBuilder();

        try {
            return $qb
                ->select('m', 'm_sv')
                ->from('App\Entity\MembershipSV', 'm_sv')
                ->innerJoin('m_sv.membership', 'm')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('m', ':membership'),
                        $qb->expr()->lt('m_sv.started', ':endedAt')
                    )
                )
                ->setParameter('membership', $membership)
                ->setParameter('endedAt', $cycle->getEndedAt()->format('Y-m-d H:i:s'))
                ->setMaxResults(1)
                ->orderBy('m_sv.started', 'DESC')
                ->addOrderBy('m_sv.id', 'DESC')
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    // /**
    //  * @return MembershipSV[] Returns an array of MembershipSV objects
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
    public function findOneBySomeField($value): ?MembershipSV
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
