<?php

namespace App\Repository;

use App\Entity\ProductCote;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductCote|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductCote|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductCote[]    findAll()
 * @method ProductCote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductCoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductCote::class);
    }

    /**
     * Get the last activate "cote" of a specific product
     *
     * @param Product $product
     * @return ProductCote|null
     */
    public function getLastProductCote(Product $product): ?ProductCote
    {
        $qb = $this->_em->createQueryBuilder();

        try {
            return $qb
                    ->select('pc')
                    ->from(ProductCote::class, 'pc')
                    ->innerJoin('pc.product', 'p')
                    ->where(
                        $qb->expr()->andX(
                            $qb->expr()->eq('p.name', ':productName'),
                            $qb->expr()->eq('pc.state', ':state')
                        )
                    )
                    ->setParameters([
                        'productName' => $product->getName(),
                        'state' => true,
                    ])
                    ->setMaxResults(1)
                    ->orderBy('pc.id', 'DESC')
                    ->getQuery()
                    ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }


    // /**
    //  * @return ProductCote[] Returns an array of ProductCote objects
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
    public function findOneBySomeField($value): ?ProductCote
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
