<?php

namespace App\Repository;

use App\Entity\Cycle;
use App\Entity\ProductSV;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Cache;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductSV|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductSV|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductSV[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductSVRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductSV::class);
    }

    /**
     * @return ProductSV[]|null
     */
    public function findAll()
    {
        $qb = $this->createQueryBuilder('psv');

        return $qb
                ->select('p', 'psv')
                ->innerJoin('psv.product', 'p')
                ->getQuery()
                ->setCacheable(true)
                ->setCacheMode(Cache::MODE_NORMAL)
                ->getResult()
            ;
    }

    /**
     * @param array $products
     * @return ProductSV[]|null
     */
    public function getSvOfProducts($products)
    {
        $qb = $this->_em->createQueryBuilder();

        return  $qb
            ->select('p', 'psv')
            ->from(ProductSV::class, 'psv')
            ->innerJoin('psv.product', 'p')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->in('p.id', ':products'),
                    $qb->expr()->eq('psv.status', ':status')
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
     * @return ProductSV|null
     */
    public function productSV(int $product, Cycle $cycle)
    {
        $qb = $this->_em->createQueryBuilder();

        try {
            return $qb
                ->select('p', 'p_sv')
                ->from(ProductSV::class, 'p_sv')
                ->innerJoin('p_sv.product', 'p')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('p.id', ':product_id'),
                        $qb->expr()->lt('p_sv.startedAt', ':endedAt')
                    )
                )
                ->setParameter('product_id', $product)
                ->setParameter('endedAt', $cycle->getEndedAt()->format('Y-m-d H:i:s'))
                ->setMaxResults(1)
                ->orderBy('p_sv.startedAt', 'DESC')
                ->addOrderBy('p_sv.id', 'DESC')
                ->getQuery()
                ->setCacheable(true)
                ->setCacheMode(Cache::MODE_NORMAL)
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Get the last activate distributor price of a specific product
     *
     * @param Product $product
     * @return ProductSV|null
     */
    public function getLastProductSV(Product $product): ?ProductSV
    {
        $qb = $this->_em->createQueryBuilder();

        try {
            return $qb
                    ->select('psv')
                    ->from(ProductSV::class, 'psv')
                    ->innerJoin('psv.product', 'p')
                    ->where(
                        $qb->expr()->andX(
                            $qb->expr()->eq('p', ':productName'),
                            $qb->expr()->eq('psv.status', ':status')
                        )
                    )
                    ->setParameters([
                        'productName' => $product->getCode(),
                        'status' => true,
                    ])
                    ->setMaxResults(1)
                    ->orderBy('psv.id', 'DESC')
                    ->getQuery()
                    ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}
