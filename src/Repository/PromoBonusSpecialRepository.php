<?php

namespace App\Repository;

use App\Entity\Grade;
use App\Entity\PromoBonusSpecial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method PromoBonusSpecial|null find($id, $lockMode = null, $lockVersion = null)
 * @method PromoBonusSpecial|null findOneBy(array $criteria, array $orderBy = null)
 * @method PromoBonusSpecial[]    findAll()
 * @method PromoBonusSpecial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromoBonusSpecialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromoBonusSpecial::class);
    }

    /**
     * @param string $endCycle
     * @param string|null $grade
     * @return PromoBonusSpecial[]|null
     */
   /* public function getPromoMatchingCycle(string $endCycle, string $grade = null): ?array
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->addSelect('b', 'eg')
            ->innerJoin('p.bonusSpecial', 'b')
            ->innerJoin('p.eligibleGrade', 'eg')
            ->andWhere(
                $qb->expr()->andX(
                    $qb->expr()->eq('p.status', ':status'),
                    $qb->expr()->lt('p.startedAt', ':startedAt'),
                    $qb->expr()->gte('p.endedAt', ':endedAt')
                )
            );

        if ($grade) {
            $qb
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->eq('eg.commercialName',':grade'),
                    $qb->expr()->eq('eg.commercialName',':grade')
                ))
                ->setParameter('grade', $grade);
        }

        return $qb
                ->setParameter('startedAt', $endCycle)
                ->setParameter('endedAt', $endCycle)
                ->setParameter('status', true)
                ->orderBy('p.id', 'DESC')
                ->getQuery()
                ->getResult()
            ;
    }*/
}
