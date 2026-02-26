<?php

namespace App\Repository;

use App\Entity\PrestationService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrestationService|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrestationService|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrestationService[]    findAll()
 * @method PrestationService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrestationServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrestationService::class);
    }

    /**
     * @param string $prestationCode
     * @return PrestationService|null
     */
    public function getLastPrestationService(string $prestationCode): ?PrestationService
    {
        $qb = $this->_em->createQueryBuilder();

        try {
            return $qb
                ->select('ps')
                ->from(PrestationService::class, 'ps')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('ps.code', ':prestationCode'),
                        $qb->expr()->eq('ps.status', ':status')
                    )
                )
                ->setParameters([
                    'prestationCode' => $prestationCode,
                    'status' => true,
                ])
                ->setMaxResults(1)
                ->orderBy('ps.id', 'DESC')
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}
