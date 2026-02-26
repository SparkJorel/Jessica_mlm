<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserBinaryCycle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserBinaryCycle|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserBinaryCycle|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserBinaryCycle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserBinaryCycleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserBinaryCycle::class);
    }

    /**
     * @return UserBinaryCycle[]|null
     */
    public function findAll()
    {
        return $this
                    ->createQueryBuilder('ubc')
                    ->addSelect('u', 'c')
                    ->innerJoin('ubc.user', 'u')
                    ->innerJoin('ubc.cycle', 'c')
                    ->orderBy('c.id', 'DESC')
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @param User $user
     * @return UserBinaryCycle[]|null
     */
    public function findByUser(User $user)
    {
        return $this
                    ->createQueryBuilder('ubc')
                    ->addSelect('u', 'c')
                    ->innerJoin('ubc.user', 'u')
                    ->innerJoin('ubc.cycle', 'c')
                    ->where('u = :user')
                    ->setParameter('user', $user)
                    ->orderBy('c.id', 'DESC')
                    ->getQuery()
                    ->getResult();
    }
}
