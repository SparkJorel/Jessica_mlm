<?php

namespace App\Repository;

use App\Entity\CommandProducts;
use App\Entity\Cycle;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommandProducts|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommandProducts|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommandProducts[]    findAll()
 * @method CommandProducts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommandProducts::class);
    }

    /**
     * @param User $user
     * @param Cycle $cycle
     * @return CommandProducts[]|null
     */
    public function getVolumeAchatByUserByCycle(User $user, Cycle $cycle): ?array
    {
        $qb = $this->_em->createQueryBuilder();
        return
                    $qb
                        ->select('p', 'c', 'u', 'cp')
                        ->from('App\Entity\CommandProducts', 'cp')
                        ->innerJoin('cp.product', 'p')
                        ->innerJoin('cp.command', 'c')
                        ->innerJoin('c.user', 'u')
                        ->where(
                            $qb->expr()->andX(
                                $qb->expr()->eq('u', ':user'),
                                $qb->expr()->gte('c.dateCommand', ':start'),
                                $qb->expr()->lte('c.dateCommand', ':end'),
                                $qb->expr()->lte('c.motif', ':motif')
                            )
                        )
                        ->setParameters([
                            'user' => $user,
                            'start' => $cycle->getStartedAt(),
                            'end' => $cycle->getEndedAt(),
                            'motif' => 'Achat',
                        ])
                        ->orderBy('c.dateCommand', 'ASC')
                        ->getQuery()
                        ->getResult();
    }

    /**
     * @param Cycle $cycle
     * @return integer[]|null
     */
    public function getProductsBoughtByCycle(Cycle $cycle): ?array
    {
        $qb = $this->_em->createQueryBuilder();

        $product_ids =
            $qb
                ->select('p.id', 'cp')
                ->from('App\Entity\CommandProducts', 'cp')
                ->innerJoin('cp.product', 'p')
                ->innerJoin('cp.command', 'c')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->gte('c.dateCommand', ':start'),
                        $qb->expr()->lte('c.dateCommand', ':end'),
                        $qb->expr()->lte('c.motif', ':motif')
                    )
                )
                ->setParameters([
                    'start' => $cycle->getStartedAt(),
                    'end' => $cycle->getEndedAt(),
                    'motif' => 'Achat',
                ])
                ->getQuery()
                ->getResult('ColumnHydrator');

        return array_unique($product_ids);
    }

    /**
     * @param Cycle $cycle
     * @param bool|null $paid
     * @return CommandProducts[]
     */
    public function getAllProductsBoughtByCycle(Cycle $cycle, bool $paid)
    {
        $qb = $this->_em->createQueryBuilder();

        return
            $qb
                ->select('cp', 'p')
                ->from(CommandProducts::class, 'cp')
                ->innerJoin('cp.product', 'p')
                ->innerJoin('cp.command', 'c')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->gte('c.dateCommand', ':start'),
                        $qb->expr()->lte('c.dateCommand', ':end'),
                        $qb->expr()->eq('c.motif', ':motif'),
                        $qb->expr()->eq('c.paid', ':paid')
                    )
                )
                ->setParameter('start', $cycle->getStartedAt()->format('Y-m-d H:i:s'))
                ->setParameter('end', $cycle->getEndedAt()->format('Y-m-d H:i:s'))
                ->setParameter('motif', 'Achat')
                ->setParameter('paid', $paid)
                ->getQuery()
                ->getResult()
            ;
    }


    // /**
    //  * @return CommandProducts[] Returns an array of CommandProducts objects
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
    public function findOneBySomeField($value): ?CommandProducts
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
