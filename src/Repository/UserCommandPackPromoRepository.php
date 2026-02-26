<?php

namespace App\Repository;

use App\Entity\Cycle;
use App\Entity\User;
use App\Entity\UserCommandPackPromo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserCommandPackPromo|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserCommandPackPromo|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserCommandPackPromo[]    findAll()
 * @method UserCommandPackPromo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserCommandPackPromoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCommandPackPromo::class);
    }

    public function getAchatPackPromoByUserByCycle(Cycle $cycle, User $user = null)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb
            ->select('p', 'u', 'ucpp')
            ->from(UserCommandPackPromo::class, 'ucpp')
            ->innerJoin('ucpp.member', 'u')
            ->innerJoin('ucpp.pack', 'p')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->gte('ucpp.dateCommand', ':start'),
                    $qb->expr()->lte('ucpp.dateCommand', ':end')
                )
            );

        if ($user) {
            $qb->andWhere(
                $qb->expr()->eq('u', ':user')
            )
                ->setParameter('user', $user);
        }

        return
            $qb
                ->setParameter('start', $cycle->getStartedAt()->format('Y-m-d H:i:s'))
                ->setParameter('end', $cycle->getEndedAt()->format('Y-m-d H:i:s'))
                ->orderBy('u.fullname', 'ASC')
                ->getQuery()
                ->getResult()
            ;
    }

    /**
     * @param string $code
     * @return integer|null
     */
    public function getCount(string $code)
    {
        try {
            $total =  $this
                ->_em
                ->createQuery('SELECT  COUNT(ucpp.id) 
                                          FROM  App\Entity\UserCommandPackPromo ucpp
                                          WHERE ucpp.code LIKE :code')
                ->setParameter('code', $code.'%')
                ->getSingleScalarResult();
            ;

            if (!$total) {
                $total = 0;
            }
        } catch (NonUniqueResultException $e) {
            return null;
        }

        return (int)$total;
    }


    // /**
    //  * @return UserCommandPackPromo[] Returns an array of UserCommandPackPromo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserCommandPackPromo
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
