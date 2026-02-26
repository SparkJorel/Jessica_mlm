<?php

namespace App\Repository;

use App\Entity\Cycle;
use App\Entity\Membership;
use App\Entity\MembershipSubscription;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MembershipSubscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method MembershipSubscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method MembershipSubscription[]    findAll()
 * @method MembershipSubscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MembershipSubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MembershipSubscription::class);
    }

    /**
     * @param User|null $user
     * @param array|null $criterias
     * @return Query
     */
    public function getAllMembershipSubscription(User $user = null, array $criterias = null)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb
            ->select('u', 'm', 'ms')
            ->from('App\Entity\MembershipSubscription', 'ms')
            ->innerJoin('ms.member', 'u')
            ->innerJoin('ms.membership', 'm')
            ;
        if ($user) {
            $qb->where(
                $qb->expr()->eq('u', ':user')
            )
             ->setParameter('user', $user);
        }

        if ($criterias) {
            foreach ($criterias as $key => $criteria) {
                if ($criteria) {
                    if ('fullname' === $key) {
                        $qb
                            ->andwhere(
                                $qb->expr()->orX(
                                    $qb->expr()->like('u.fullname', ':term'),
                                    $qb->expr()->like('u.email', ':term'),
                                    $qb->expr()->like('u.username', ':term')
                                )
                            )->setParameter('term', '%'.$criteria.'%');
                    } else {
                        $qb
                            ->andWhere(
                                $qb->expr()->eq("u.{$key}", ":{$key}")
                            )
                            ->setParameter($key, $criteria);
                    }
                }
            }
        }

        return
            $qb
                ->getQuery();
    }

    /**
     * @param User $createdAt
     * @return MembershipSubscription[]|null
     */
    public function getAllMembershipSubscriptionOfUser(User $createdAt): ?array
    {
        $qb = $this->_em->createQueryBuilder();

        return
        $qb
            ->select('c', 'u', 'm', 'ms')
            ->from('App\Entity\MembershipSubscription', 'ms')
            ->innerJoin('ms.member', 'u')
            ->innerJoin('ms.createdBy', 'c')
            ->innerJoin('ms.membership', 'm')
            ->where(
                $qb->expr()->eq('u', ':user')
            )
             ->setParameter('user', $createdAt)
             ->getQuery()
             ->getResult()
             ;
    }

    /**
     * @return MembershipSubscription[]|null
     */
    public function getAllMembershipSubscriptionUsingPackConsommateur(): ?array
    {
        $qb = $this->_em->createQueryBuilder();

        return
        $qb
            ->select('u', 'm', 'ms')
            ->from('App\Entity\MembershipSubscription', 'ms')
            ->innerJoin('ms.member', 'u')
            ->innerJoin('ms.membership', 'm')
            ->where(
                $qb->expr()->eq('m.coefficent', ':coefficient')
            )
             ->setParameter('coefficient', 1)
             ->getQuery()
             ->getResult()
             ;
    }

    /**
     * @param User $user
     * @param Cycle $cycle
     * @return MembershipSubscription[]
     */
    public function getSubscription(User $user, Cycle $cycle)
    {
        $qb = $this->_em->createQueryBuilder();
        return $qb
                        ->select('u', 'm', 'ms')
                        ->from('App\Entity\MembershipSubscription', 'ms')
                        ->innerJoin('ms.member', 'u')
                        ->innerJoin('ms.membership', 'm')
                        ->where(
                            $qb->expr()->andX(
                                $qb->expr()->eq('u', ':user'),
                                $qb->expr()->gte('ms.paidAt', ':start'),
                                $qb->expr()->gte('ms.endedAt', ':end')
                            )
                        )
                        ->setParameters([
                            'user' => $user,
                            'start' => $cycle->getStartedAt(),
                            'end' => $cycle->getEndedAt()
                        ])
                        ->orderBy('ms.id', 'ASC')
                        ->getQuery()
                        ->getResult();
    }

    /**
     * @param Cycle $cycle
     * @param array $users
     * @return MembershipSubscription[]|null
     */
    public function getAllMemberSubscriptionOfCycle(Cycle $cycle, array $users = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb
            ->select('u', 'm', 'ms')
            ->from('App\Entity\MembershipSubscription', 'ms')
            ->innerJoin('ms.member', 'u')
            ->innerJoin('ms.membership', 'm')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->gte('ms.paidAt', ':start'),
                    $qb->expr()->lte('ms.paidAt', ':end')
                )
            );
        if ($users) {
            $qb
                ->andWhere(
                    $qb->expr()->in('u.id', ':users')
                )
                ->setParameter('users', $users)
            ;
        }
        return
            $qb
                ->setParameter('start', $cycle->getStartedAt()->format('Y-m-d H:i:s'))
                ->setParameter('end', $cycle->getEndedAt()->format('Y-m-d H:i:s'))
                ->orderBy('u.fullname', 'ASC')
                ->addOrderBy('m.coefficent', 'ASC')
                ->getQuery()
                ->getResult();
    }
  
    /**
     * @param Cycle $cycle
     * @param array $users
     * @return MembershipSubscription[]|null
     */
    public function getMembershipSubscriptionOfSpecificLevel(Cycle $cycle, array $users = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb
            ->select('u', 'm', 'sp', 'ms')
            ->from('App\Entity\MembershipSubscription', 'ms')
            ->innerJoin('ms.member', 'u')
            ->innerJoin('u.sponsor', 'sp')
            ->innerJoin('ms.membership', 'm')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->gte('ms.paidAt', ':start'),
                    $qb->expr()->lte('ms.paidAt', ':end')
                )
            );
        if ($users) {
            $qb
                ->andWhere(
                    $qb->expr()->in('sp.id', ':users')
                )
                ->setParameter('users', $users)
            ;
        }
        return
            $qb
                ->setParameter('start', $cycle->getStartedAt()->format('Y-m-d H:i:s'))
                ->setParameter('end', $cycle->getEndedAt()->format('Y-m-d H:i:s'))
                ->orderBy('u.fullname', 'ASC')
                ->addOrderBy('m.coefficent', 'ASC')
                ->getQuery()
                ->getResult();
    }


    /**
     * @param array $membership_subscriptions
     * @return MembershipSubscription[]|null
     */
    public function getListMemberSubscriptions(array $membership_subscriptions)
    {
        $qb = $this->_em->createQueryBuilder();
        return
                $qb
                    ->select('u', 'm', 'ms')
                    ->from('App\Entity\MembershipSubscription', 'ms')
                    ->innerJoin('ms.member', 'u')
                    ->innerJoin('ms.membership', 'm')
                    ->where(
                        $qb->expr()->in('ms.id', ':membership_subscriptions')
                    )
                    ->setParameter('membership_subscriptions', $membership_subscriptions)
                    ->orderBy('u.fullname', 'ASC')
                    ->addOrderBy('m.coefficent', 'ASC')
                    ->getQuery()
                    ->getResult();
    }


    /**
     * @param Cycle $cycle
     * @param array $users
     * @return float|null
     */
    public function getSumSVMatchingMemberSubscriptionOfCycle(Cycle $cycle, array $users = null)
    {
        $qb = $this->createQueryBuilder('ms');
        $qb
            ->select('SUM(ms.totalSVBinaire) as totalSVBin')
            ->innerJoin('ms.member', 'u')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->gte('ms.paidAt', ':start'),
                    $qb->expr()->lte('ms.paidAt', ':end')
                )
            );
        if ($users) {
            $qb
                ->andWhere(
                    $qb->expr()->in('u.id', ':users')
                )
                ->setParameter('users', $users)
            ;
        }
        return
            $qb
                ->setParameter('start', $cycle->getStartedAt()->format('Y-m-d H:i:s'))
                ->setParameter('end', $cycle->getEndedAt()->format('Y-m-d H:i:s'))
                ->getQuery()
                ->getSingleScalarResult();
    }


    /**
     * @param Cycle $cycle
     * @param array $users
     * @return float|null
     */
    public function getSumPricesMatchingMemberSubscriptionOfCycle(Cycle $cycle, array $users = null)
    {
        $qb = $this->createQueryBuilder('ms');
        $qb
            ->select('SUM(ms.price) as totalPrices')
            ->innerJoin('ms.member', 'u')
            ->andWhere(
                $qb->expr()->andX(
                    $qb->expr()->gte('ms.paidAt', ':start'),
                    $qb->expr()->lte('ms.paidAt', ':end')
                )
            );
        if ($users) {
            $qb
                ->andWhere(
                    $qb->expr()->in('u.id', ':users')
                )
                ->setParameter('users', $users)
            ;
        }
        return
            $qb
                ->setParameter('start', $cycle->getStartedAt()->format('Y-m-d H:i:s'))
                ->setParameter('end', $cycle->getEndedAt()->format('Y-m-d H:i:s'))
                ->getQuery()
                ->getSingleScalarResult();
    }


    /**
     * @param User $user
     * @return MembershipSubscription|null
     */
    public function checkUpgrade(User $user)
    {
        $qb = $this->_em->createQueryBuilder();
        try {
            return $qb
               ->select('u', 'm', 'ms')
               ->from('App\Entity\MembershipSubscription', 'ms')
               ->innerJoin('ms.member', 'u')
               ->innerJoin('ms.membership', 'm')
               ->where(
                   $qb->expr()->andX(
                       $qb->expr()->eq('u', ':user'),
                       $qb->expr()->eq('ms.state', ':state'),
                       $qb->expr()->eq('ms.upgraded', ':upgraded')
                   )
               )
               ->setParameters([
                   'user' => $user,
                   'state' => false,
                   'upgraded' => true
               ])
               ->setMaxResults(1)
               ->orderBy('ms.id', 'DESC')
               ->getQuery()
               ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }


    /**
     * @param User $user
     * @param Cycle $cycle
     * @return Membership|null
     */
    public function getUserMembershipSubscription(User $user, Cycle $cycle)
    {
        $qb = $this->_em->createQueryBuilder();
        return $qb
                            ->select('u', 'm', 'ms')
                            ->from('App\Entity\MembershipSubscription', 'ms')
                            ->innerJoin('ms.member', 'u')
                            ->innerJoin('ms.membership', 'm')
                            ->where(
                                $qb->expr()->andX(
                                    $qb->expr()->eq('u', ':user'),
                                    $qb->expr()->lte('ms.endedAt', ':end')
                                )
                            )
                            ->setParameters([
                                'user' => $user,
                                'start' => $cycle->getStartedAt()->format('Y-m-d H:i:s'),
                                'end' => $cycle->getEndedAt()->format('Y-m-d H:i:s')
                            ])
                            ->orderBy('u.fullname', 'ASC')
                            ->addOrderBy('m.coefficent', 'ASC')
                            ->getQuery()
                            ->getResult();
    }

    /**
     * @param User $user
     * @param Cycle $cycle
     * @return MembershipSubscription|null
     */
    public function getMembershipSubscriptionMatchingCycle(User $user, Cycle $cycle)
    {
        $qb = $this->_em->createQueryBuilder();
        try {
            return $qb
                ->select('u', 'm', 'ms')
                ->from(MembershipSubscription::class, 'ms')
                ->innerJoin('ms.member', 'u')
                ->innerJoin('ms.membership', 'm')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('u', ':user'),
                        $qb->expr()->lt('ms.startedAt', ':endedAt')
                    )
                )
                ->setParameters(
                    [
                        'user' => $user,
                        'endedAt' => $cycle->getEndedAt()->format('Y-m-d H:i:s')
                    ]
                )
                ->setMaxResults(1)
                ->orderBy('ms.startedAt', 'DESC')
                ->addOrderBy('ms.id', 'DESC')
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @param User $user
     * @return MembershipSubscription|null
     */
    public function getLastUserMembershipSubscription(User $user): ?MembershipSubscription
    {
        try {
            return $this
                ->createQueryBuilder('ms')
                ->addSelect('u', 'm')
                ->innerJoin('ms.member', 'u')
                ->innerJoin('ms.membership', 'm')
                ->where('u = :user')
                ->setParameter('user', $user)
                ->setMaxResults(1)
                ->orderBy('ms.id', 'DESC')
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @param User $user
     * @return MembershipSubscription|null
     */
    public function getLastUserMembershipSubscriptionActivated(User $user): ?MembershipSubscription
    {
        $qb = $this->_em->createQueryBuilder();

        try {
            return
                    $qb->select('ms', 'u', 'm')
                        ->from(MembershipSubscription::class, 'ms')
                        ->innerJoin('ms.member', 'u')
                        ->innerJoin('ms.membership', 'm')
                        ->where(
                            $qb->expr()->andX(
                                $qb->expr()->eq('u', ':user'),
                                $qb->expr()->isNotNull('ms.endedAt')
                            )
                        )
                        ->setParameter('user', $user)
                        ->setMaxResults(1)
                        ->orderBy('ms.id', 'DESC')
                        ->getQuery()
                        ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}
