<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserMonthCarryOver;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserMonthCarryOver|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserMonthCarryOver|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserMonthCarryOver[]    findAll()
 * @method UserMonthCarryOver[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserMonthCarryOverRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMonthCarryOver::class);
    }

    /**
     * @param User $user
     * @param string $month
     * @return UserMonthCarryOver|null
     */
    public function getCarryOver(User $user, string $month, string $year, string $startedAt = null, string $endedAt = null)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb
            ->select('u', 'co')
            ->from(UserMonthCarryOver::class, 'co')
            ->innerJoin('co.user', 'u')
            ->where(
                $qb->expr()->andX(
                  $qb->expr()->eq('u', ':user'),
                  $qb->expr()->eq('co.month', ':month'),
                  $qb->expr()->eq('co.year', ':year')
              )
            );


        if ($startedAt) {
            $qb
              ->andWhere(
                  $qb->expr()->eq('co.startedAt', ':startedAt')
              )
              ->setParameter('startedAt', $startedAt);
        }

        if ($endedAt) {
            $qb
              ->andWhere(
                  $qb->expr()->eq('co.endedAt', ':endedAt')
              )
              ->setParameter('endedAt', $endedAt);
        }

        try {
            return $qb
                    ->setParameter('user', $user)
                    ->setParameter('month', $month)
                    ->setParameter('year', $year)
                    ->setMaxResults(1)
                    ->orderBy('co.id', 'DESC')
                    ->getQuery()
                    ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}
