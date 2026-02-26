<?php

namespace App\Repository;

use App\Entity\Cycle;
use App\Entity\Grade;
use App\Entity\User;
use App\Entity\UserGrade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Cache;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserGrade|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserGrade|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserGrade[]    findAll()
 * @method UserGrade[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserGradeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserGrade::class);
    }

    /**
     * @param User $user
     * @return UserGrade|null
     */
    public function getLastUserGrade(User $user)
    {
        $qb = $this->_em->createQueryBuilder();

        try {
            return $qb
                ->select('ug', 'u', 'g')
                ->from(UserGrade::class, 'ug')
                ->innerJoin('ug.user', 'u')
                ->innerJoin('ug.grade', 'g')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('u', ':user'),
                        $qb->expr()->eq('ug.status', ':status')
                    )
                )
                ->setParameter('user', $user)
                ->setParameter('status', true)
                ->setMaxResults(1)
                ->orderBy('ug.id', 'DESC')
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @param User $user
     * @param Cycle $cycle
     * @return UserGrade|null
     */
    public function getUserGradeMatchingCycle(User $user, Cycle $cycle)
    {
        $qb = $this->_em->createQueryBuilder();

        try {
            return $qb
                ->select('ug', 'u', 'g', 'bgs')
                ->from(UserGrade::class, 'ug')
                ->innerJoin('ug.user', 'u')
                ->innerJoin('ug.grade', 'g')
                ->leftJoin('g.gradeBGs', 'bgs')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('u', ':user'),
                        $qb->expr()->lt('ug.startedAt', ':endedAt')
                    )
                )
                ->setParameter('user', $user)
                ->setParameter('endedAt', $cycle->getEndedAt()->format('Y-m-d H:i:s'))
                ->setMaxResults(1)
                ->orderBy('ug.startedAt', 'DESC')
                ->getQuery()
                ->enableResultCache(300)
                ->setCacheable(true)
                ->setCacheMode(Cache::MODE_NORMAL)
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @param Cycle $cycle
     * @return AbstractQuery
     */
    public function getUserGradeChange(Cycle $cycle = null): AbstractQuery
    {
        $qb = $this->_em->createQueryBuilder();

        $qb
                    ->select('ug', 'u', 'g')
                    ->from(UserGrade::class, 'ug')
                    ->innerJoin('ug.user', 'u')
                    ->innerJoin('ug.grade', 'g');
        if ($cycle) {
            $qb
                ->where(
                    $qb->expr()->eq('ug.startedAt', ':startedAt')
                )
                ->setParameter(
                    'startedAt',
                    $cycle->getStartedAt()->format('Y-m-d H:i:s')
                );
        }
        return  $qb->getQuery();
    }

    /**
     * @param User $user
     * @return Query
     */
    public function getAllUserGrade(User $user)
    {
        $qb = $this->_em->createQueryBuilder();
        return
                $qb
                            ->select('ug', 'u', 'g')
                            ->from(UserGrade::class, 'ug')
                            ->innerJoin('ug.user', 'u')
                            ->innerJoin('ug.grade', 'g')
                            ->where(
                                $qb->expr()->eq('u', ':user')
                            )
                            ->setParameter('user', $user)
                            ->getQuery()
                            ->enableResultCache(300)
                            ->setCacheable(true)
                            ->setCacheMode(Cache::MODE_NORMAL)
                            ;
    }

    /**
     * @param User $user
     * @param Grade $grade
     * @return UserGrade|null
     */
    public function checkUserGrade(User $user, Grade $grade)
    {
        $qb = $this->_em->createQueryBuilder();
        try {
            return
                $qb
                    ->select('ug', 'u', 'g')
                    ->from(UserGrade::class, 'ug')
                    ->innerJoin('ug.user', 'u')
                    ->innerJoin('ug.grade', 'g')
                    ->where(
                        $qb->expr()->andX(
                            $qb->expr()->eq('u', ':user'),
                            $qb->expr()->eq('g', ':grade')
                        )
                    )
                    ->setParameter('user', $user)
                    ->setParameter('grade', $grade)
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult()
                ;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    // /**
    //  * @return UserGrade[] Returns an array of UserGrade objects
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
    public function findOneBySomeField($value): ?UserGrade
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
