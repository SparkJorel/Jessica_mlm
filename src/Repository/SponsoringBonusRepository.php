<?php

namespace App\Repository;

use App\Entity\Cycle;
use App\Entity\SponsoringBonus;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Cache;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SponsoringBonus|null find($id, $lockMode = null, $lockVersion = null)
 * @method SponsoringBonus|null findOneBy(array $criteria, array $orderBy = null)
 * @method SponsoringBonus[]    findAll()
 * @method SponsoringBonus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SponsoringBonusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SponsoringBonus::class);
    }

    public function getBonusSponsoringByUser(User $user)
    {
        return $this
                        ->createQueryBuilder('b')
                        ->addSelect('s')
                        ->innerJoin("b.sponsor", "s")
                        ->where("s = :user")
                        ->setParameter("user", $user)
                        ->orderBy("b.id", "DESC")
                        ->getQuery()
                        ->getResult();
    }

    /**
     * @param User $user
     * @param Cycle $cycle
     * @param bool|null $paid
     * @return SponsoringBonus[]
     */
    public function getBonusSponsoringByUserByCycle(User $user, Cycle $cycle, bool $paid = null)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb
                ->select('b', 's')
                ->from(SponsoringBonus::class, 'b')
                ->innerJoin("b.sponsor", "s")
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('s', ':user'),
                        $qb->expr()->gte('b.dateActivation', ':start'),
                        $qb->expr()->lte('b.dateActivation', ':end')
                    )
                );
        if ($paid) {
            $qb->andWhere(
                $qb->expr()->eq('b.paid', ':paid')
            )
                     ->setParameter('paid', $paid);
        }
        return
                      $qb
                        ->setParameter("user", $user)
                        ->setParameter("start", $cycle->getStartedAt())
                        ->setParameter("end", $cycle->getEndedAt())
                        ->orderBy("b.id", "DESC")
                        ->getQuery()
                        ->getResult();
    }

    public function getBonusSponsoringByCycle(Cycle $cycle)
    {
        $qb = $this->_em->createQueryBuilder();

        return $qb
                        ->select('b', 's')
                        ->from(SponsoringBonus::class, 'b')
                        ->innerJoin("b.sponsor", "s")
                        ->where(
                            $qb->expr()->andX(
                                $qb->expr()->gte('b.dateActivation', ':start'),
                                $qb->expr()->lte('b.dateActivation', ':end')
                            )
                        )
                        ->setParameters(
                            [
                                "start" => $cycle->getStartedAt(),
                                "end" => $cycle->getEndedAt(),
                            ]
                        )
                        ->orderBy("b.id", "DESC")
                        ->getQuery()
                        ->getResult();
    }

    public function getBonusSponsoring()
    {
        return $this
                        ->createQueryBuilder('b')
                        ->addSelect('s')
                        ->innerJoin("b.sponsor", "s")
                        ->orderBy("b.id", "DESC")
                        ->getQuery()
                        ->getResult();
    }
}
