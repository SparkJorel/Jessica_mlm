<?php

namespace App\Repository;

use App\Entity\UserBonusSpecial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserBonusSpecial|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserBonusSpecial|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserBonusSpecial[]    findAll()
 * @method UserBonusSpecial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserBonusSpecialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserBonusSpecial::class);
    }
}
