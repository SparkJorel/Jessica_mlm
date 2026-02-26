<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\ORM\Cache;
use App\Entity\ProductClientPrice;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method ProductClientPrice|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductClientPrice|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductClientPrice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductClientPriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductClientPrice::class);
    }

    /**
     * @return ProductClientPrice[]|null
     */
    public function findAll()
    {
        $qb = $this->createQueryBuilder('pcp');

        return $qb
            ->select('p', 'pcp')
            ->innerJoin('pcp.product', 'p')
            ->getQuery()
            ->setCacheable(true)
            ->setCacheMode(Cache::MODE_NORMAL)
            ->getResult()
            ;
    }

    /**
     * Get the last activate client price of a specific product
     *
     * @param Product $product
     * @return ProductClientPrice|null
     */
    public function getLastProductClientPrice(Product $product): ?ProductClientPrice
    {
        $qb = $this->_em->createQueryBuilder();

        try {
            return $qb
                    ->select('pcp')
                    ->from(ProductClientPrice::class, 'pcp')
                    ->innerJoin('pcp.product', 'p')
                    ->where(
                        $qb->expr()->andX(
                            $qb->expr()->eq('p.name', ':productName'),
                            $qb->expr()->eq('pcp.status', ':status')
                        )
                    )
                    ->setParameters([
                        'productName' => $product->getName(),
                        'status' => true,
                    ])
                    ->setMaxResults(1)
                    ->orderBy('pcp.id', 'DESC')
                    ->getQuery()
                    ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }


    // /**
    //  * @return ProductClientPrice[] Returns an array of ProductClientPrice objects
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
    public function findOneBySomeField($value): ?ProductClientPrice
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
