<?php

namespace App\Repository;

use App\Entity\Cycle;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cycle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cycle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cycle[]    findAll()
 * @method Cycle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CycleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cycle::class);
    }

    /**
     * @return Cycle[]|null
     */
    public function getAllCycle(): ?array
    {
        return $this
                    ->createQueryBuilder('c')
                    ->orderBy('c.id', 'DESC')
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @return Cycle|null
     */
    public function getLastCycle(): ?Cycle
    {
        try {
            return $this
                ->createQueryBuilder('c')
                ->setMaxResults(1)
                ->orderBy('c.id', 'DESC')
                ->getQuery()
                ->getOneOrNullResult();
		  
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @param User $user
     * @return Cycle[]
     */
    public function getCycles(User $user)
    {
        $qb = $this->_em->createQueryBuilder();

        return $qb
                ->select('c')
                ->from(Cycle::class, 'c')
                ->where(
                    $qb->expr()->gte('c.endedAt', ':dateActivation')
                )
                ->setParameter('dateActivation', $user->getDateActivation()->format('Y-m-d H:i:s'))
                ->orderBy('c.id', 'DESC')
                ->getQuery()
                ->getResult();
    }

    /**
     * @param Cycle $cycle
     * @return Cycle|null
     */
    public function getCycleBefore(Cycle $cycle)
    {
        $qb = $this->_em->createQueryBuilder();

        return $qb
                    ->select('c')
                    ->from(Cycle::class, 'c')
                    ->where(
                        $qb->expr()->lt('c.endedAt', ':startedAt')
                    )
                    ->setParameter('startedAt', $cycle->getStartedAt()->format('Y-m-d H:i:s'))
                    ->setMaxResults(1)
                    ->orderBy('c.endedAt', 'DESC')
                    ->getQuery()
                    ->getOneOrNullResult();
    }
}
