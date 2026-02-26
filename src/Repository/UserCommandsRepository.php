<?php

namespace App\Repository;

use App\Entity\Cycle;
use App\Entity\User;
use App\Entity\UserCommands;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\DBAL\DBALException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserCommands|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserCommands|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserCommands[]    findAll()
 * @method UserCommands[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserCommandsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCommands::class);
    }

    /**
     * @param Cycle $cycle
     * @param User|null $user
     * @param bool|null $paid
     * @return UserCommands[]|null
     */
    public function getAllCommandsByCycle(Cycle $cycle, User $user = null, bool $paid = null): ?array
    {
        $qb = $this->_em->createQueryBuilder();

        $qb
            ->select('uc', 'u', 'cps')
            ->from(UserCommands::class, 'uc')
            ->innerJoin('uc.user', 'u')
            ->leftJoin('uc.products', 'cps')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->gte('uc.dateCommand', ':start'),
                    $qb->expr()->lte('uc.dateCommand', ':end'),
                    $qb->expr()->eq('uc.motif', ':motif')
                )
            );

        if ($user) {
            $qb->andWhere(
                $qb->expr()->eq('u', ':user')
            )
            ->setParameter('user', $user);
        }

        if ($paid) {
            $qb->andWhere(
                $qb->expr()->eq('uc.paid', ':paid')
            )
            ->setParameter('paid', $paid);
        }

        return
            $qb
                ->setParameter('start', $cycle->getStartedAt()->format('Y-m-d H:i:s'))
                ->setParameter('end', $cycle->getEndedAt()->format('Y-m-d H:i:s'))
                ->setParameter('motif', 'Achat')
                ->orderBy('uc.dateCommand', 'ASC')
                ->getQuery()
                ->getResult()
                ;
    }

    /**
     * @param Cycle $cycle
     * @param User $user
     * @return UserCommands[]|null
     */
    public function obtenirLesCommandesPersonnellesEtDesPacksConsommateursDuCycle(Cycle $cycle, User $user): ?array
    {
        $qb = $this->_em->createQueryBuilder();

        $qb
            ->select('uc', 'u', 'm', 'cps')
            ->from(UserCommands::class, 'uc')
            ->innerJoin('uc.user', 'u')
            ->innerJoin('u.membership', 'm')
            ->leftJoin('uc.products', 'cps')
            ->andWhere(
                $qb->expr()->eq('u', ':user'),
                $qb->expr()->gte('uc.dateCommand', ':start'),
                $qb->expr()->lte('uc.dateCommand', ':end'),
                $qb->expr()->eq('uc.motif', ':motif')
            )
            ->orWhere(
                $qb->expr()->andX(
                    $qb->expr()->eq('u.sponsor', ':sponsor'),
                    $qb->expr()->eq('m.coefficent', ':coefficent'),
                    $qb->expr()->gte('uc.dateCommand', ':start'),
                    $qb->expr()->lte('uc.dateCommand', ':end'),
                    $qb->expr()->eq('uc.motif', ':motif')
                )
            );
      

        return
            $qb
                ->setParameter('start', $cycle->getStartedAt()->format('Y-m-d H:i:s'))
                ->setParameter('end', $cycle->getEndedAt()->format('Y-m-d H:i:s'))
                ->setParameter('motif', 'Achat')
                ->setParameter('user', $user)
                ->setParameter('sponsor', $user)
                ->setParameter('coefficent', 1)
                ->orderBy('uc.dateCommand', 'ASC')
                ->getQuery()
                ->getResult()
                ;
    }    
  
    /**
     * @param int $sponsor_id
     * @param string $position
     * @return false|int
     * @throws DBALException
     */
    public function getNextAutoIncrementValue()
    {
        $conn = $this->_em->getConnection();

        $sql = "
            SELECT `auto_increment` FROM INFORMATION_SCHEMA.TABLES
			WHERE table_name = 'user_commands'
        ";

        try {
            $stmt = $conn->query($sql);
        } catch (DBALException $e) {
            return false;
        }


         return $stmt->fetch();
    }

    /**
     * @param Cycle $cycle
     * @param int[] $users
     * @param bool $paid
     * @return UserCommands[]|null
     */
    public function getAllCommandsByCycleGroupOfUsers(Cycle $cycle, array $users, bool $paid): ?array
    {
        $qb = $this->_em->createQueryBuilder();
        return
            $qb
                ->select('uc', 'u', 'cps')
                ->from(UserCommands::class, 'uc')
                ->innerJoin('uc.user', 'u')
                ->leftJoin('uc.products', 'cps')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->gte('uc.dateCommand', ':start'),
                        $qb->expr()->lte('uc.dateCommand', ':end'),
                        $qb->expr()->eq('uc.motif', ':motif'),
                        $qb->expr()->in('u.id', ':users'),
                        $qb->expr()->eq('uc.paid', ':paid')
                    )
                )
                ->setParameter('start', $cycle->getStartedAt()->format('Y-m-d H:i:s'))
                ->setParameter('end', $cycle->getEndedAt()->format('Y-m-d H:i:s'))
                ->setParameter('motif', 'Achat')
                ->setParameter('users', $users)
                ->setParameter('paid', $paid)
                ->orderBy('u.fullname', 'ASC')
                ->getQuery()
                ->getResult()
                ;
    }

    /**
     * @param Cycle $cycle
     * @param int $left
     * @param int $right
     * @param bool|null $paid
     * @return UserCommands[]
     */
    public function getAllNetworkCycleCommands(Cycle $cycle, int $left, int $right, bool $paid = null)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb
            ->select('uc', 'u', 'cps')
            ->from(UserCommands::class, 'uc')
            ->innerJoin('uc.user', 'u')
            ->leftJoin('uc.products', 'cps')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->gte('uc.dateCommand', ':start'),
                    $qb->expr()->lte('uc.dateCommand', ':end'),
                    $qb->expr()->eq('uc.motif', ':motif')
                )
            )
            ->andWhere(
                $qb->expr()->andX(
                    $qb->expr()->gte('u.lft', ':left'),
                    $qb->expr()->lte('u.rgt', ':right')
                )
            );

        if ($paid) {
            $qb->andWhere(
                $qb->expr()->eq('uc.paid', ':paid')
            )
                ->setParameter('paid', $paid);
        }

        return
            $qb
                ->setParameter('start', $cycle->getStartedAt()->format('Y-m-d H:i:s'))
                ->setParameter('end', $cycle->getEndedAt()->format('Y-m-d H:i:s'))
                ->setParameter('motif', 'Achat')
                ->setParameter('left', $left)
                ->setParameter('right', $right)
                ->orderBy('uc.dateCommand', 'ASC')
                ->getQuery()
                ->getResult()
                ;
    }


    /**
     * @param Cycle $cycle
     * @param bool|null $paid
     * @return UserCommands[]
     */
    public function getProductsBoughtByCycle(Cycle $cycle, bool $paid = null)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb
            ->select('uc', 'cps', 'u')
            ->from(UserCommands::class, 'uc')
            ->innerJoin('uc.user', 'u')
            ->leftJoin('uc.products', 'cps')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->gte('uc.dateCommand', ':start'),
                    $qb->expr()->lte('uc.dateCommand', ':end'),
                    $qb->expr()->eq('uc.motif', ':motif')
                )
            );

        if ($paid) {
            $qb->andWhere(
                $qb->expr()->eq('uc.paid', ':paid')
            )
            ->setParameter('paid', $paid);
        }

        return
            $qb
                ->setParameter('start', $cycle->getStartedAt()->format('Y-m-d H:i:s'))
                ->setParameter('end', $cycle->getEndedAt()->format('Y-m-d H:i:s'))
                ->setParameter('motif', 'Achat')
                ->orderBy('uc.dateCommand', 'ASC')
                ->getQuery()
                ->getResult()
                ;
    }

    /**
     * Finds carts that have not been modified since the given date.
     *
     * @param \DateTime $limitDate
     * @param int $limit
     *
     * @return UserCommands[]|null
     */
    public function findCartsNotModifiedSince(\DateTime $limitDate, int $limit = 10): ?array
    {
        return $this->createQueryBuilder('uc')
                    ->addSelect('cps')
                    ->leftJoin('uc.products', 'cps')
                    ->andWhere('uc.status = :status')
                    ->andWhere('uc.dateCommandUpdate < :date')
                    ->setParameter('status', UserCommands::STATUS_CART)
                    ->setParameter('date', $limitDate)
                    ->setMaxResults($limit)
                    ->getQuery()
                    ->getResult()
        ;
    }


    /**
     * @return integer|null
     */
    public function getCount()
    {
        try {
            /** @var UserCommands|null $result */
            $result =  $this->createQueryBuilder('uc')
                            ->setMaxResults(1)
                            ->orderBy('uc.id', 'DESC')
                            ->getQuery()
                            ->getOneOrNullResult()
            ;

        } catch (NoResultException $e) {
            return 0;
        } catch (NonUniqueResultException $e) {
            return null;
        }

        if (!$result) {
            return 0;
        }

        return $result->getId();
    }


    public function getLastCommandNotPaid(User $user, string $status)
    {
        $qb = $this->createQueryBuilder('uc');

        return $qb
                ->addSelect('u')
                ->innerJoin('uc.user', 'u')
                //->leftJoin('uc.products', 'cp')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('u', ':user'),
                        $qb->expr()->eq('uc.distributor', ':distributor'),
                        $qb->expr()->eq('uc.status', ':status')
                    )
                )
                ->setParameter('user', $user)
                ->setParameter('distributor', true)
                ->setParameter('status', $status)
                ->orderBy('uc.id', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
    }
}
