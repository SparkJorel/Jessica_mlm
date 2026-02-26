<?php

namespace App\Repository;

use App\Entity\Membership;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Membership|null find($id, $lockMode = null, $lockVersion = null)
 * @method Membership|null findOneBy(array $criteria, array $orderBy = null)
 * @method Membership[]    findAll()
 * @method Membership[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MembershipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Membership::class);
    }

    /**
     * @return Membership|null
     */
    public function getLastMembership()
    {
        $qb = $this->_em->createQueryBuilder();
        try {
            return $qb
                ->select('m')
                ->from(Membership::class, 'm')
                ->orderBy('m.coefficent', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
		  
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    public function getMembershipForUpgrade($member_id)
    {
        $coeff = $this
                        ->_em
                        ->getRepository(User::class)
                        ->find($member_id)
                        ->getMembership()
                        ->getCoefficent()
        ;

        $qb = $this->createQueryBuilder('m');

        return $qb->where(
            $qb->expr()->gt('m.coefficent', ':coeff')
        )
                    ->setParameter('coeff', $coeff)
                    ->orderBy('m.coefficent', 'DESC')
            ;
    }

    /**
     * @param $coeff
     * @return Membership[]|null
     */
    public function getMembershipUp($coeff)
    {
        $qb = $this->createQueryBuilder('m');

        return $qb->where(
            $qb->expr()->gt('m.coefficent', ':coeff')
        )
                    ->setParameter('coeff', $coeff)
                    ->orderBy('m.coefficent', 'DESC')
                    ->getQuery()
                    ->getResult()
            ;
    }

    public function getPreviousMembership(int $coeff)
    {
        $qb = $this->createQueryBuilder('m');
        try {
            return  $qb->where(
                $qb->expr()->lt('m.coefficent', ':coeff')
            )
            ->setParameter('coeff', $coeff)
            ->orderBy('m.coefficent', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
             ;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    // /**
    //  * @return Membership[] Returns an array of Membership objects
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
    public function findOneBySomeField($value): ?Membership
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
