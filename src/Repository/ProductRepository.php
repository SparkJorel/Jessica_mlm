<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Get the last version of product that is currently used
     *
     * @param Product $product
     * @return Product|null
     */
    public function getLastProduct(Product $product): ?Product
    {
        $qb = $this->_em->createQueryBuilder();

        try {
            return $qb
                        ->select('p')
                        ->from(Product::class, 'p')
                        ->where(
                            $qb->expr()->andX(
                                $qb->expr()->eq('p.name', ':productName'),
                                $qb->expr()->eq('p.status', ':status')
                            )
                        )
                        ->setParameters([
                            'productName' => $product->getName(),
                            'status' => true
                        ])
                        ->setMaxResults(1)
                        ->orderBy('p.id', 'DESC')
                        ->getQuery()
                        ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @param string $term
     * @return Product[]|null
     */
    public function getAllProductsAndDistributorPrices(string $term): ?array
    {
        $qb = $this->_em->createQueryBuilder();

        return $qb
                    ->select('p')
                    ->from(Product::class, 'p')
                    ->where(
                        $qb->expr()->orX(
                            $qb->expr()->like('p.code', ':term'),
                            $qb->expr()->like('p.name', ':term')
                        )
                    )
                    ->andWhere(
                        $qb->expr()->eq('p.status', true)
                    )
                    ->orderBy('p.code', 'ASC')
                    ->setParameter('term', '%'.$term.'%')
                    ->getQuery()
                    ->getResult();
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
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
    public function findOneBySomeField($value): ?Product
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
