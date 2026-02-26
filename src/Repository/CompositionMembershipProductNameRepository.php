<?php

namespace App\Repository;

use App\Entity\CompositionMembershipProductName;
use App\Entity\Membership;
use App\Entity\MembershipProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CompositionMembershipProductName|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompositionMembershipProductName|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompositionMembershipProductName[]    findAll()
 * @method CompositionMembershipProductName[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompositionMembershipProductNameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompositionMembershipProductName::class);
    }

    public function getAvailablePackName(Membership $membership)
    {
        /** @var MembershipProductRepository $membershipProductRepository */
        $membershipProductRepository = $this->_em->getRepository(MembershipProduct::class);

        $qb = $this->createQueryBuilder('cmpn');

        $mb_ids = $membershipProductRepository->getAvailablePackName($membership);

        if (!$mb_ids) {
            $mb_ids = [];
        }

        return $qb->where($qb->expr()->in('cmpn.id', ':mb_ids'))
                    ->setParameter('mb_ids', $mb_ids)
                    ->orderBy('cmpn.name', 'ASC');
    }

    // /**
    //  * @return CompositionMembershipProductName[] Returns an array of CompositionMembershipProductName objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CompositionMembershipProductName
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
