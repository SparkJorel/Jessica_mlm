<?php

namespace App\Repository;

use App\Entity\Cycle;
use App\Entity\User;
use App\Entity\UserPaidBonus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method UserPaidBonus|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPaidBonus|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPaidBonus[]    findAll()
 * @method UserPaidBonus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserPaidBonusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPaidBonus::class);
    }

    /**
     * @param User $user
     * @param Cycle $cycle
     * @param string $reason
     * @return bool
     */
    public function getStatusBonusBinaireCycle(User $user, string $reason, Cycle $cycle)
    {
        $qb = $this->_em->createQueryBuilder();
        try {
            /**
             * @var UserPaidBonus $userPaid
             */
            $userPaid =
                $qb
                ->select('upb', 'u')
                ->from(UserPaidBonus::class, 'upb')
                ->join('upb.user', 'u')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('u', ':user'),
                        $qb->expr()->eq('upb.month', ':month'),
                        $qb->expr()->eq('upb.year', ':year'),
                        $qb->expr()->eq('upb.startedAt', ':startedAt'),
                        $qb->expr()->eq('upb.endedAt', ':endedAt'),
                        $qb->expr()->eq('upb.reason', ':motif')
                    )
                )
                ->setParameters([
                    'user' => $user,
                    'month' => $cycle->getEndedAt()->format('F'),
                    'year' => $cycle->getEndedAt()->format('Y'),
                    'startedAt' => $cycle->getStartedAt()->format('Y-m-d H:i:s'),
                    'endedAt' => $cycle->getEndedAt()->format('Y-m-d H:i:s'),
                    'motif' => $reason
                ])
                ->getQuery()
                ->getOneOrNullResult();

            if ($userPaid) {
                return $userPaid->getPaid();
            } else {
                return false;
            }
        } catch (NonUniqueResultException $e) {
            return false;
        }
    }

    // /**
    //  * @return UserPaidBonus[] Returns an array of UserPaidBonus objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserPaidBonus
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
