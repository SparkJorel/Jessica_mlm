<?php

namespace App\Repository;

use App\Entity\Cycle;
use App\Entity\User;
use Doctrine\DBAL\DBALException;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends NestedTreeRepository
{
    public function getCities()
    {
        $qb = $this->_em->createQueryBuilder();
        return $qb
            ->select('u.city')
            ->from(User::class, 'u')
            ->groupBy('u.city')
            ->getQuery()
            //                ->setCacheable(true)
            //                ->setCacheMode(Cache::MODE_NORMAL)
            ->getResult();
    }

    public function getTree()
    {
        $em = $this->_em;

        /** @var UserRepository $repo */
        $repo = $em->getRepository('App\Entity\User');
        $query = $em
            ->createQueryBuilder()
            ->select('u')
            ->from('App\Entity\User', 'u')
            ->orderBy('u.root, u.lft', 'ASC')
            ->where('u.root = 1')
            ->getQuery();
        $options = array(
            'decorate' => true,
            'rootOpen' => '<ul id="data-ul">',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>',
            'nodeDecorator' => function ($u) {
                return '<a href="/page/' . $u['id'] . '">' . $u['username'] . '</a>';
            }
        );

        $tree = $repo->buildTree($query->getArrayResult(), $options);
        return $tree;
    }

    /**
     * @param int $sponsor_id
     * @param string $position
     * @return false|int
     * @throws DBALException
     */
    public function findUplineKnowingSponsor(int $sponsor_id, string $position)
    {
        $conn = $this->_em->getConnection();

        $sql = "
            SELECT @pv:=u.id as u_id, u.upline_id 
            FROM (select * from user order by id asc) u 
            JOIN (select @pv:= :sponsor) tmp 
            WHERE u.upline_id = @pv and u.position = :position 
            ORDER BY u_id DESC LIMIT 1
        ";

        try {
            $stmt = $conn->prepare($sql);
        } catch (DBALException $e) {
            return false;
        }

        $stmt->execute(
            [
                'sponsor' => $sponsor_id,
                'position' => $position
            ]
        );

        return $stmt->fetch();
    }

    /**
     * @param User $user
     * @param Cycle $cycle
     * @return User[]|null
     */
    public function getDirectChildrenOfUser(User $user, Cycle $cycle)
    {
        $qb = $this->_em->createQueryBuilder();

        return $qb
            ->select('u', 'up')
            ->from(User::class, 'u')
            ->innerJoin('u.upline', 'up')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('up', ':user'),
                    $qb->expr()->lte('u.dateActivation', ':endedAt')
                )
            )
            ->setParameter('user', $user)
            ->setParameter('endedAt', $cycle->getEndedAt()->format('Y-m-d H:i:s'))
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $term
     * @param int $left
     * @param int $right
     * @param bool $upline
     * @return User[]|null
     */
    public function getSponsorOrUplineList(string $term, int $left, int $right, bool $upline = false): ?array
    {
        $qb = $this->createQueryBuilder('u')
                    ->addSelect('m')
                    ->innerJoin('u.membership', 'm');
        $qb
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->like('u.fullname', ':term'),
                    $qb->expr()->like('u.email', ':term'),
                    $qb->expr()->like('u.username', ':term')
                )
            );

        if (!$upline) {
            $qb
                ->andWhere(
                    $qb->expr()->andX(
                        $qb->expr()->gte('u.lft', ':left'),
                        $qb->expr()->lte('u.rgt', ':right')
                    )
                )
                ->setParameter('left', $left)
                ->setParameter('right', $right);
        } else {
            $qb
                ->andWhere(
                    $qb->expr()->andX(
                        $qb->expr()->gt('u.lft', ':left'),
                        $qb->expr()->lt('u.rgt', ':right')
                    )
                )
                ->setParameter('left', $left)
                ->setParameter('right', $right);
        }
        return
            $qb
            ->setParameter('term', '%' . $term . '%')
            ->getQuery()
            ->getResult();
    }


    /**
     * @param string $term
     * @return User[]|null
     */
    public function getAllMembersList(string $term): ?array
    {
        $qb = $this
                ->createQueryBuilder('u')
                ->addSelect('m')
                ->innerJoin('u.membership', 'm');

        $qb
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->like('u.fullname', ':term'),
                    $qb->expr()->like('u.email', ':term'),
                    $qb->expr()->like('u.username', ':term')
                )
            );

        return
            $qb
            ->setParameter('term', '%' . $term . '%')
            ->getQuery()
            ->getResult();
    }



    /**
     * @param string $username
     * @return User|null
     */
    public function checkUsername(string $username): ?User
    {
        $qb = $this->createQueryBuilder('u');
        $qb
            ->where(
                $qb->expr()->eq('u.username', ':username')
            );

        try {
            return
                $qb
                ->setParameter('username', $username)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @param integer[] $users
     * @return User[]|null
     */
    public function getUsersMatchingLevel(array $users)
    {
        $qb = $this->createQueryBuilder('u');
        return $qb
            ->innerJoin('u.sponsor', 's')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->in('s.id', ':users')
                )
            )
            ->setParameter('users', $users)
            ->orderBy('u.lvl', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $left
     * @param int $right
     * @param int $lvl
     * @return User[]|null
     */
    public function getUsersMatchingLevelOldWay(int $left, int $right, int $lvl): ?array
    {
        $qb = $this->createQueryBuilder('u');
        return $qb
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->gt('u.lft', ':left'),
                    $qb->expr()->lt('u.rgt', ':right'),
                    $qb->expr()->lte('u.lvl', ':level')
                )
            )
            ->setParameter('left', $left)
            ->setParameter('right', $right)
            ->setParameter('level', $lvl)
            ->orderBy('u.lvl', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $left
     * @param int $right
     * @param Cycle|null $cycle
     * @return User[]|null
     */
    public function getUserNetwork(int $left, int $right, Cycle $cycle = null)
    {
        $qb = $this->createQueryBuilder('u');
        $qb
            ->andWhere(
                $qb->expr()->andX(
                    $qb->expr()->gte('u.lft', ':left'),
                    $qb->expr()->lte('u.rgt', ':right')
                )
            );

        if ($cycle) {
            $qb->andWhere(
                $qb->expr()->lte('u.dateActivation', ':dateActivation')
            )
                ->setParameter('dateActivation', $cycle->getEndedAt()->format('Y-m-d H:i:s'));
        }

        return
            $qb
            ->setParameter('left', $left)
            ->setParameter('right', $right)
            ->orderBy('u.lvl', 'ASC')
            ->addOrderBy('u.lft', 'ASC')
            ->addOrderBy('u.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array|null $criterias
     * @return Query
     */
    public function getAllUsers(array $criterias = null)
    {
        $qb = $this->createQueryBuilder('u');
        $qb
            ->addSelect('up', 's', 'm')
            ->leftJoin('u.upline', 'up')
            ->leftJoin('u.sponsor', 's')
            ->innerJoin('u.membership', 'm');

        if ($criterias) {
            foreach ($criterias as $key => $value) {
                if ($value) {
                    if ('fullname' === $key) {
                        $qb->andWhere(
                            $qb->expr()->orX(
                                $qb->expr()->like('u.fullname', ':term'),
                                $qb->expr()->like('u.email', ':term'),
                                $qb->expr()->like('u.username', ':term')
                            )
                        )->setParameter('term', '%' . $value . '%');
                    } else {
                        $qb->andWhere(
                            $qb->expr()->eq("u.{$key}", ":{$key}")
                        )
                            ->setParameter($key, $value);
                    }
                }
            }
        }

        return $qb
            ->orderBy('u.fullname', 'ASC')
            ->getQuery();
    }

    /**
     * @return User[]|null
     */
    public function getAllActivatedNetworkers()
    {
        $qb = $this->createQueryBuilder('u');
        return $qb
            ->addSelect('up', 's', 'm')
            ->leftJoin('u.upline', 'up')
            ->leftJoin('u.sponsor', 's')
            ->innerJoin('u.membership', 'm')
            ->andWhere($qb->expr()->andX(
                $qb->expr()->eq('u.activated', ':activated'),
                $qb->expr()->neq('m.coefficent', ':coefficent')
            ))
            ->setParameter('activated', true)
            ->setParameter('coefficent', 1)
            ->orderBy('u.fullname', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param User $user
     * @param string $position
     * @return User|string
     */
    public function validateUserPosition(User $user, string $position)
    {
        $qb = $this->_em->createQueryBuilder();

        try {
            return $qb
                ->select('u', 'up')
                ->from(User::class, 'u')
                ->innerJoin('u.upline', 'up')
                ->where(
                    $qb->expr()->andX(
                        $qb->expr()->eq('up', ':upline'),
                        $qb->expr()->eq('u.position', ':position')
                    )
                )
                ->setParameters([
                    'upline' => $user,
                    'position' => $position
                ])
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param string $yearMonth
     * @return int|null
     */
    public function countMonthlySubscription(string $yearMonth)
    {
        $qb = $this->_em->createQueryBuilder();

        try {
            $total =  $qb
                ->select('count(u.id)')
                ->from(User::class, 'u')
                ->where("DATE_FORMAT(u.dateActivation, '%y%m') = :yearMonth")
                ->setParameter('yearMonth', $yearMonth)
                ->getQuery()
                ->getSingleScalarResult();

            return $total;
        } catch (NoResultException $e) {
            return 0;
        } catch (NonUniqueResultException $ex) {
            return null;
        }
    }

    /**
     * @return User[]|null
     */
    public function getAllActivatedUsers(): ?array
    {
        $qb = $this->_em->createQueryBuilder();
        return
            $qb
            ->select('u')
            ->from(User::class, 'u')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('u.activated', ':activated'),
                    $qb->expr()->isNull('u.codeDistributor')
                )
            )
            ->orderBy('u.dateActivation', 'ASC')
            ->setParameter('activated', true)
            ->getQuery()
            ->getResult();
    }
}
