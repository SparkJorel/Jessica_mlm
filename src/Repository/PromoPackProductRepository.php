<?php

namespace App\Repository;

use App\Entity\PromoPackProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Cache;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PromoPackProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method PromoPackProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method PromoPackProduct[]    findAll()
 * @method PromoPackProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromoPackProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromoPackProduct::class);
    }

    /**
     * @param array $pack
     * @return PromoPackProduct[]
     */
    public function getProductsConcernedBySV($pack): array
    {
        $qb = $this->_em->createQueryBuilder();
        return $qb
                            ->select('p', 'pr', 'ppp')
                            ->from('App\Entity\PromoPackProduct', 'ppp')
                            ->innerJoin('ppp.product', 'p')
                            ->innerJoin('ppp.promo', 'pr')
                            ->where($qb->expr()->in('pr.id', ':packs'))
                            ->setParameter('packs', $pack)
                            ->getQuery()
                            ->setCacheable(true)
                            ->setCacheMode(Cache::MODE_NORMAL)
                            ->getResult()
            ;
    }

    // /**
    //  * @return PromoPackProduct[] Returns an array of PromoPackProduct objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PromoPackProduct
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
