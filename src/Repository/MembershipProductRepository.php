<?php

namespace App\Repository;

use App\Entity\Membership;
use App\Entity\MembershipProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MembershipProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method MembershipProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method MembershipProduct[]    findAll()
 * @method MembershipProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MembershipProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MembershipProduct::class);
    }

    /**
     * @param Membership $membership
     * @return int[]|null
     */
    public function getAvailablePackName(Membership $membership): ?array
    {
        $qb = $this->_em->createQueryBuilder();

        $results =
                $qb
                    ->select('n.id', 'm', 'mb')
                    ->from(MembershipProduct::class, 'mb')
                    ->innerJoin('mb.name', 'n')
                    ->innerJoin('mb.membership', 'm')
                    ->where(
                        $qb->expr()->eq('m', ':membership')
                    )
                    ->setParameter('membership', $membership)
                    ->getQuery()
                    ->getResult('ColumnHydrator');

        if (!$results) {
            return null;
        }

        return array_unique($results);
    }


    // /**
    //  * @return MembershipProduct[] Returns an array of MembershipProduct objects
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
    public function findOneBySomeField($value): ?MembershipProduct
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
