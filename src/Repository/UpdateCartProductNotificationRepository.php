<?php

namespace App\Repository;

use App\Entity\UpdateCartProductNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UpdateCartProductNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method UpdateCartProductNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method UpdateCartProductNotification[]    findAll()
 * @method UpdateCartProductNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UpdateCartProductNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UpdateCartProductNotification::class);
    }
}
