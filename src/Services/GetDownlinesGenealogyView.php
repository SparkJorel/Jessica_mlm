<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

class GetDownlinesGenealogyView
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var TokenStorageInterface
     */
    private $token;

    public function __construct(
        EntityManagerInterface $manager,
        TokenStorageInterface $token,
        Environment $twig
    )
    {
        $this->manager = $manager;
        $this->twig = $twig;
        $this->token = $token;
    }

    /**
     * @param User|null $userConnected
     * @return string
     */
    public function getAllChildren(User $userConnected = null)
    {
        $users = [];

        if (!$userConnected) {
            /** @var User $userConnected */
            $userConnected = $this->token->getToken()->getUser();
        }

        $users['start'] = $userConnected->getLvl();
        $users['id'] = $userConnected->getEmail();
        $users['name'] = $userConnected->getFullname();
        $users['data'] = [];
        $users['children'] = [];

        /**
         * @var UserRepository $repository
         */
        $repository = $this->manager->getRepository(User::class);

        $partners = $repository->getUserNetwork(
            $userConnected->getLft(),
            $userConnected->getRgt()
        );

        return  $repository->childrenHierarchy(
            $userConnected,
            false,
            [
                'decorate' => true,
                'rootOpen' => '<ul id="ul-data">',
                'rootClose' => '</ul>',
                'childOpen' => '<li>',
                'childClose' => '</li>',
                'nodeDecorator'  => function ($u) {
                    return '<a href="/page/'.$u['id'].'">'.$u['username'].'</a>';
                }
            ],
            true
        );
    }


    /**
     * @param User|null $userConnected
     * @return string
     */
    public function getAllChildren2(User $userConnected = null)
    {
        if (!$userConnected) {
            $userConnected = $this->token->getToken()->getUser();
        }

        /** @var NestedTreeRepository $repository */
        $repository = $this->manager->getRepository(User::class);

        return  $repository->childrenHierarchy(
            $userConnected,
            false,
            [
                'decorate' => false,
                'rootOpen' => '<ul id="ul-data">',
                'rootClose' => '</ul>',
                'childOpen' => '<li>',
                'childClose' => '</li>',
                'nodeDecorator' => function ($node) {
                    return $node['fullname'];
                },
                'childSort' => [
                    ['field' => 'lvl', 'dir' => 'asc'],
                    ['field' => 'upline', 'dir' => 'asc'],
                    ['field' => 'position', 'dir' => 'asc']
                ]
            ],
            true
        );
    }

    /**
     * @param User $userConnected
     * @param bool $include_node
     * @return Query
     */
    public function getAllChildrenAsRows(User $userConnected = null, $include_node = false)
    {
        if (!$userConnected) {
            $userConnected = $this->token->getToken()->getUser();
        }

        /**
         * @var NestedTreeRepository $repository
         */
        $repository = $this->manager->getRepository(User::class);
        $qb = $repository
            ->childrenQueryBuilder($userConnected, false, null, 'ASC', $include_node)
            ->join('App\Entity\User', 'parent', Join::WITH, 'parent.lft <= node.lft AND parent.rgt >= node.lft')
            ->join('App\Entity\Membership', 'membership', Join::WITH, 'membership = parent.membership')
            ->leftJoin('App\Entity\User', 'upline', Join::WITH, 'upline = parent.upline')
            ->select('parent.id, parent.title, parent.entryDate, parent.activated, parent.gender, parent.position, parent.mobilePhone, 
                                        parent.state, parent.fullname, parent.username, parent.email, parent.city, 
                                        parent.country, membership.name as membership_name, upline.id as upline_id, 
                                        parent.lft, parent.rgt, parent.lvl, count(parent.id) as nw')
            ->groupBy('parent.id')
            ->orderBy('parent.id', 'ASC')
        ;


        return $qb->getQuery();
    }


    /**
     * @param Query $query
     * @return array|string
     */
    public function getAllChildrenOrderedByPosition(Query $query)
    {
        $rows = $query->getArrayResult();
        /**
         * @var NestedTreeRepository $repository
         */
        $repository = $this
                            ->manager->getRepository(User::class);

        return $repository->buildTree($rows, [
            'decorate' => false,
            'childSort' => [
                ['field' => 'lvl', 'dir' => 'asc'],
                ['field' => 'upline_id', 'dir' => 'asc'],
                ['field' => 'position', 'dir' => 'asc']
            ]
        ]);
    }

    /**
     * @param User|null $userConnected
     * @return string|array
     */
    public function getAllChildren3(User $userConnected = null)
    {
        if (!$userConnected) {
            /** @var User $userConnected */
            $userConnected = $this->token->getToken()->getUser();
        }

        $query = $this->getAllChildrenAsRows($userConnected, true);
        return $this->getAllChildrenOrderedByPosition($query);
    }


    /**
     * @param User $userConnected
     * @return User[]
     */
    public function getAllChildrenAsObjects(User $userConnected = null)
    {
        $this->manager->getConfiguration()->addCustomHydrationMode(
            'tree',
            'Gedmo\Tree\Hydrator\ORM\TreeObjectHydrator'
        );

        if (!$userConnected) {
            /** @var User $userConnected */
            $userConnected = $this->token->getToken()->getUser();
        }

        return $this->manager->createQueryBuilder()
            ->select('node')
            ->from(User::class, 'node')
            ->orderBy('node.lvl, node.position', 'ASC')
            ->where('node = :user')
            ->setParameter('user', $userConnected)
            ->getQuery()
            ->setHint(Query::HINT_INCLUDE_META_COLUMNS, true)
            ->getResult('tree');
    }
}
