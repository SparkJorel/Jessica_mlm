<?php

namespace App\Repository;

use App\Entity\BonusSpecial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BonusSpecial|null find($id, $lockMode = null, $lockVersion = null)
 * @method BonusSpecial|null findOneBy(array $criteria, array $orderBy = null)
 * @method BonusSpecial[]    findAll()
 * @method BonusSpecial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BonusSpecialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BonusSpecial::class);
    }

    /**
     * Get the last activate Bonus Special
     *
     * @param BonusSpecial $bonusSpecial
     * @return BonusSpecial|null
     */
    public function getLastBonusSpecial(BonusSpecial $bonusSpecial): ?BonusSpecial
    {
        $qb = $this->_em->createQueryBuilder();

        try {
            return $qb
                ->select('bs')
                ->from(BonusSpecial::class, 'bs')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('bs', ':bonusSpecial'),
                        $qb->expr()->eq('bs.status', ':status')
                    )
                )
                ->setParameters([
                    'bonusSpecial' => $bonusSpecial,
                    'status' => true,
                ])
                ->setMaxResults(1)
                ->orderBy('bs.id', 'DESC')
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}
