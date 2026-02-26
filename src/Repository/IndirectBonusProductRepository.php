<?php

namespace App\Repository;

use App\Entity\IndirectBonusProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method IndirectBonusProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method IndirectBonusProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method IndirectBonusProduct[]    findAll()
 * @method IndirectBonusProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class IndirectBonusProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IndirectBonusProduct::class);
    }
}
