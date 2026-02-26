<?php

namespace App\Repository;

use App\Entity\CommissionIndirectBonus;
use App\Entity\Cycle;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommissionIndirectBonus|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommissionIndirectBonus|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommissionIndirectBonus[]    findAll()
 * @method CommissionIndirectBonus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommissionIndirectBonusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommissionIndirectBonus::class);
    }

    /**
     * @param array $user
     * @param Cycle $cycle
     * @return CommissionIndirectBonus[]|null
     */
    public function getUserCommissionIndirectBonus(array $user, Cycle $cycle): ?array
    {
        $qb = $this->createQueryBuilder('cib');

        return $qb
                    ->addSelect('u')
                    ->innerJoin('cib.user', 'u')
                    ->andWhere(
                        $qb->expr()->andX(
                            $qb->expr()->in('u.id', ':user'),
                            $qb->expr()->eq('cib.startedAt', ':startedAt'),
                            $qb->expr()->eq('cib.endedAt', ':endedAt')
                        )
                    )
                    ->setParameters([
                        'user' => $user,
                        'startedAt' => $cycle->getStartedAt(),
                        'endedAt' => $cycle->getEndedAt()
                    ])
                    ->orderBy('u.fullname', 'ASC')
                    ->addOrderBy('cib.lvl', 'ASC')
                    ->getQuery()
                    ->getResult();
    }

    // /**
    //  * @return CommissionIndirectBonus[] Returns an array of CommissionIndirectBonus objects
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
    public function findOneBySomeField($value): ?CommissionIndirectBonus
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
