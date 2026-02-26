<?php

namespace App\Repository;

use App\Entity\Cycle;
use App\Entity\Product;
use App\Entity\ProductDistributorPrice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Cache;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductDistributorPrice|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductDistributorPrice|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductDistributorPrice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductDistributorPriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductDistributorPrice::class);
    }

    /**
     * @return ProductDistributorPrice[]|null
     */
    public function findAll()
    {
        $qb = $this->createQueryBuilder('pdp');

        return $qb
            ->select('p', 'pdp')
            ->innerJoin('pdp.product', 'p')
            ->getQuery()
            ->setCacheable(true)
            ->setCacheMode(Cache::MODE_NORMAL)
            ->getResult()
            ;
    }


    /**
     * @param string $code
     * @return ProductDistributorPrice|null
     */
    public function findPriceProduct(string $code)
    {
        $qb = $this->createQueryBuilder('pdp');
        try {
            return $qb
                ->select('p', 'pdp')
                ->innerJoin('pdp.product', 'p')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('p.code', ':code'),
                        $qb->expr()->eq('pdp.status', true)
                    )
                )
                ->setParameter('code', $code)
                ->getQuery()
                ->getOneOrNullResult()
                ;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @param string $term
     * @return ProductDistributorPrice[]|null
     */
    public function getAllProductsAndDistributorPrices(string $term): ?array
    {
        $qb = $this->_em->createQueryBuilder();
        return $qb
                        ->select('p', 'd')
                        ->from(ProductDistributorPrice::class, 'd')
                        ->innerJoin('d.product', 'p')
                        ->where(
                            $qb->expr()->orX(
                                $qb->expr()->like('p.code', ':term'),
                                $qb->expr()->like('p.name', ':term')
                            )
                        )
                        ->andWhere(
                            $qb->expr()->eq('d.status', true)
                        )
                        ->orderBy('p.code', 'ASC')
                        ->setParameter('term', '%'.$term.'%')
                        ->getQuery()
                        ->setCacheable(true)
                        ->setCacheMode(Cache::MODE_NORMAL)
                        ->getResult();
    }

    /**
     * @param array $products
     * @return ProductDistributorPrice[]|null
     */
    public function getPriceDistributorOfProducts($products)
    {
        $qb = $this->_em->createQueryBuilder();
        return  $qb
                        ->select('p', 'pdp')
                        ->from(ProductDistributorPrice::class, 'pdp')
                        ->innerJoin('pdp.product', 'p')
                        ->where(
                            $qb->expr()->andX(
                                $qb->expr()->in('p.id', ':products'),
                                $qb->expr()->in('pdp.status', ':status')
                            )
                        )
                        ->setParameters(
                            [
                                'products' => $products,
                                'status' => true
                            ]
                        )
                        ->getQuery()
                        ->setCacheable(true)
                        ->setCacheMode(Cache::MODE_NORMAL)
                        ->getResult();
    }

    /**
     * @param int $product
     * @param Cycle $cycle
     * @return ProductDistributorPrice|null
     */
    public function productCost(int $product, Cycle $cycle)
    {
        $qb = $this->_em->createQueryBuilder();

        try {
            return $qb
                ->select('p', 'pdp')
                ->from(ProductDistributorPrice::class, 'pdp')
                ->innerJoin('pdp.product', 'p')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('p.id', ':product_id'),
                        $qb->expr()->lt('pdp.applyingDate', ':endedAt')
                    )
                )
                ->setParameter('product_id', $product)
                ->setParameter('endedAt', $cycle->getEndedAt()->format('Y-m-d H:i:s'))
                ->setMaxResults(1)
                ->orderBy('pdp.applyingDate', 'DESC')
                ->addOrderBy('pdp.id', 'DESC')
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Get the last activate distributor price of a specific product
     *
     * @param Product $product
     * @return ProductDistributorPrice|null
     */
    public function getLastProductDistributorPrice(Product $product): ?ProductDistributorPrice
    {
        $qb = $this->_em->createQueryBuilder();

        try {
            return $qb
                    ->select('pdp')
                    ->from(ProductDistributorPrice::class, 'pdp')
                    ->innerJoin('pdp.product', 'p')
                    ->where(
                        $qb->expr()->andX(
                            $qb->expr()->eq('p', ':productName'),
                            $qb->expr()->eq('pdp.status', ':status')
                        )
                    )
                    ->setParameters([
                        'productName' => $product->getName(),
                        'status' => true,
                    ])
                    ->setMaxResults(1)
                    ->orderBy('pdp.id', 'DESC')
                    ->getQuery()
                    ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }



    // /**
    //  * @return ProductDistributorPrice[] Returns an array of ProductDistributorPrice objects
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
    public function findOneBySomeField($value): ?ProductDistributorPrice
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
