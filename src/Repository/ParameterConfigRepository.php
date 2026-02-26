<?php

namespace App\Repository;

use App\Entity\Cycle;
use App\Entity\ParameterConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class ParameterConfigRepository
 * @package App\Repository
 * @method ParameterConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParameterConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParameterConfig[]    findAll()
 * @method ParameterConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParameterConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParameterConfig::class);
    }

    public function getParameterValue(string $parameter, \DateTime $date)
    {
        $qb = $this->_em->createQueryBuilder();
        return $qb
                    ->select('p')
                    ->from(ParameterConfig::class, 'p')
                    ->where(
                        $qb->expr()->andX(
                            $qb->expr()->eq('p.name', ':name'),
                            $qb->expr()->lte('p.recordDate', ':date'),
                            $qb->expr()->gte('p.deactivatedDate', ':date')
                        )
                    )
                    ->setParameters(
                        [
                            'name' => $parameter,
                            'date' => $date->format('Y-m-d')
                        ]
                    )
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @return int|null
     */
    public function getCycleInterval(): ?int
    {
        try {
            $parameter =
                $this
                    ->createQueryBuilder('pc')
                    ->where('pc.name = :config_name')
                    ->andWhere('pc.status = :status')
                    ->setMaxResults(1)
                    ->orderBy('pc.id', 'DESC')
                    ->setParameters([
                        'config_name' => 'cycle_interval',
                        'status' => true
                    ])
                    ->getQuery()
                    ->getOneOrNullResult();
            /* @var $parameter ParameterConfig|null */
            return (!$parameter ? null : (int)$parameter->getValue());
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @param string $name_grade
     * @return int|null
     */
    public function getLevel(string $name_grade): ?int
    {
        try {
            $parameter =
                $this
                    ->createQueryBuilder('pc')
                    ->where('pc.name = :config_name')
                    ->andWhere('pc.status = :status')
                    ->setMaxResults(1)
                    ->orderBy('pc.id', 'DESC')
                    ->setParameters([
                        'config_name' => $name_grade,
                        'status' => true
                    ])
                    ->getQuery()
                    ->getOneOrNullResult();
            /* @var $parameter ParameterConfig|null */
            return (!$parameter ? null : (int)$parameter->getValue());
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @param string $parameter
     * @param Cycle $cycle
     * @return ParameterConfig|null
     */
    public function valueParameter(string $parameter, Cycle $cycle)
    {
        $qb = $this->_em->createQueryBuilder();

        try {
            return $qb
                ->select('p')
                ->from(ParameterConfig::class, 'p')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('p.name', ':parameter_name'),
                        $qb->expr()->eq('p.status', ':status'),
                        $qb->expr()->lt('p.recordDate', ':endedAt')
                    )
                )
                ->setParameter('parameter_name', $parameter)
                ->setParameter('status', true)
                ->setParameter('endedAt', $cycle->getEndedAt()->format('Y-m-d H:i:s'))
                ->setMaxResults(1)
                ->orderBy('p.recordDate', 'DESC')
                ->addOrderBy('p.id', 'DESC')
                ->getQuery()
                ->getOneOrNullResult();
		  
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}
