<?php

namespace App\Repository;

use App\Entity\Membership;
use App\Entity\MembershipBonusPourcentage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MembershipBonusPourcentage|null find($id, $lockMode = null, $lockVersion = null)
 * @method MembershipBonusPourcentage|null findOneBy(array $criteria, array $orderBy = null)
 * @method MembershipBonusPourcentage[]    findAll()
 * @method MembershipBonusPourcentage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MembershipBonusPourcentageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MembershipBonusPourcentage::class);
    }

    /**
     * @param Membership $membership
     * @return float|null
     */
    public function bonusMembership(Membership $membership)
    {
        $qb = $this->_em->createQueryBuilder();

        try {
            $result =  $qb
                ->select('m', 'b')
                ->from('App\Entity\MembershipBonusPourcentage', 'b')
                ->innerJoin('b.membership', 'm')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('m.id', ':membership'),
                        $qb->expr()->eq('b.state', ':status')
                    )
                )
                ->setParameter('membership', $membership)
                ->setParameter('status', true)
                ->getQuery()
                ->getOneOrNullResult();
		  
        } catch (NonUniqueResultException $e) {
            return null;
        }

        if ($result) {
            return $result->getValue();
        }

        return null;
    }

    // /**
    //  * @return MembershipBonusPourcentage[] Returns an array of MembershipBonusPourcentage objects
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
    public function findOneBySomeField($value): ?MembershipBonusPourcentage
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
