<?php

namespace App\Controller\Back\WebController;

use App\Entity\Membership;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\CheckIfUserHaveOnlyOneChild;
use App\Services\GenerateCodeAllUsers;
use App\Services\GetDownlinesGenealogyView;
use App\Services\ModelHandlers\UserHandler;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagAwareSessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Environment;

/**
 * Class UserController
 * @package App\Controller\Back\WebController
 */
class UserController
{
    /**
     * @var UserHandler
     */
    private $userHandler;

    /**
     * @var TokenStorageInterface
     */
    private $token;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(
        UserHandler $userHandler,
        TokenStorageInterface $token,
        Environment $twig,
        EntityManagerInterface $manager
    )
    {
        $this->userHandler = $userHandler;
        $this->token = $token;
        $this->manager = $manager;
        $this->twig = $twig;
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/users/all', name: 'user_list_all', options: ['expose' => true], methods: ['GET'])]
    public function index(Request $request): Response
    {
        $user = new User();
        return $this->userHandler->setEntity($user)->list($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/users/generate-code', name: 'users_generate_code', methods: ['GET'])]
    public function generateCode(
        GenerateCodeAllUsers $generate,
        RequestStack $requestStack,
        UrlGeneratorInterface $generator
    )
    {
        $message = $generate->generateUsersCode();

        $session = $requestStack->getSession();
        if ($session instanceof FlashBagAwareSessionInterface) {
            $session->getFlashBag()->add('info', $message);
        }

        return new RedirectResponse($generator->generate('user_list_all'));
    }

    #[Route('/users', name: 'user_list', methods: ['GET'])]
    public function list(GetDownlinesGenealogyView $graph): Response
    {
        $rows = $graph->getAllChildrenAsRows()->getArrayResult();
        $view = $this->twig
                            ->render(
                                'back/webcontroller/user/network.html.twig',
                                ['users'=> $rows]
                            );

        return new Response($view);
    }

    #[Route('/users/new', name: 'user_new_from_known_sponsor', methods: ['GET', 'POST'])]
    public function addMember(Request $request): Response
    {
        /**
         * @var User $sponsor
         */
        $sponsor = $this->token->getToken()->getUser();

        $user = new User();
        $user->setSponsor($sponsor);
        $user->setCreatedBy($sponsor);

        $this->userHandler->setEntity($user);
        return $this->userHandler->save($request);
    }


    #[Route('/user/{username}', name: 'user_show', methods: ['GET'], requirements: ['username' => '\w+'])]
    public function show(User $user): Response
    {
        return $this->userHandler->setEntity($user)->show();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/user/{id}/activated', name: 'user_activated', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function activateUser(User $user): Response
    {
        return $this->userHandler->redirectAfterActivation($user);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/users/activate', name: 'users_activate', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function activateUsers(Request $request, RouterInterface $router): RedirectResponse
    {
        $users_id = $request->request->get('user_checked');

        foreach ($users_id as $user_id) {
            /**
             * @var User $user
             */
            $user = $this->manager->getRepository(User::class)->find((int)$user_id);
            $this->userHandler->activate($user);
        }

        return new RedirectResponse($router->generate('user_list_all'));
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/users/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, User $user): Response
    {
        return $this->userHandler->setEntity($user)->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/users/{id}/delete', name: 'user_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, User $user): RedirectResponse
    {
        return $this
                    ->userHandler
                    ->setEntity($user)
                    ->remove($request, $csrf);
    }

    #[Route('/genealogy', name: 'genealogy_tree')]
    public function getTree(GetDownlinesGenealogyView $graph)
    {
        $networkers = $graph->getAllChildren();
        $view = $this->twig
                    ->render(
                        '/back/webcontroller/user/graph.html.twig',
                        [
                            'htmlTree' => $networkers
                        ]
                    );

        return new Response($view);
    }

    #[Route('/change/password', name: 'change_password', methods: ['GET', 'POST'])]
    public function changePassword(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $this->token->getToken()->getUser();

        return $this
                    ->userHandler
                    ->setEntity($user)
                    ->changePassword($request);
    }

    #[Route('/change/username', name: 'change_username', methods: ['GET', 'POST'])]
    public function changeUsername(Request $request)
    {
        /**
         * @var User $user
         */
        $user = $this->token->getToken()->getUser();

        return $this
                    ->userHandler
                    ->setEntity($user)
                    ->changeUsername($request);
    }

    #[Route('/testjson', name: 'testjson')]
    public function getTreeJson()
    {
        /** @var UserRepository $repository */
        $repository = $this->manager->getRepository(User::class);

        $repo = $repository->getTree();

        return new JsonResponse($repo);
    }

    #[Route('/api/genealogy/tree', name: 'api_genealogy_tree', methods: ['GET'], options: ['expose' => true])]
    public function getGenealogyTreeJson(Request $request, GetDownlinesGenealogyView $graph): JsonResponse
    {
        /** @var User $currentUser */
        $currentUser = $this->token->getToken()->getUser();

        $maxDepth = (int) $request->query->get('depth', 3);
        $rootId = $request->query->get('root');

        /** @var UserRepository $repository */
        $repository = $this->manager->getRepository(User::class);

        // Determine root user
        $rootUser = $currentUser;
        if ($rootId) {
            $roles = $this->token->getToken()->getRoleNames();
            $isAdmin = in_array('ROLE_JTWC_ADMIN', $roles) || in_array('ROLE_JTWC_USER_SECRET', $roles);

            $requestedUser = $repository->find((int) $rootId);
            if ($requestedUser) {
                // Admin can view any network; regular users can only view within their own
                if ($isAdmin || ($requestedUser->getLft() >= $currentUser->getLft() && $requestedUser->getRgt() <= $currentUser->getRgt())) {
                    $rootUser = $requestedUser;
                }
            }
        }

        // Flat query: get root + all descendants with membership name joined
        $rows = $this->manager->createQueryBuilder()
            ->select('u.id, u.fullname, u.username, u.position, u.activated, u.lft, u.rgt, u.lvl, m.name as membership_name')
            ->from(User::class, 'u')
            ->innerJoin('u.membership', 'm')
            ->where('u.lft >= :lft AND u.rgt <= :rgt')
            ->setParameter('lft', $rootUser->getLft())
            ->setParameter('rgt', $rootUser->getRgt())
            ->orderBy('u.lft', 'ASC')
            ->getQuery()
            ->getArrayResult();

        // Build binary tree from flat lft/rgt sorted rows
        // Each user's direct binary children are those with upline = this user
        // But since we don't have upline in query, use a stack-based parent lookup
        // Actually, simpler: query upline_id too
        $rows = $this->manager->createQueryBuilder()
            ->select('u.id, u.fullname, u.username, u.position, u.activated, u.lft, u.rgt, u.lvl, m.name as membership_name, IDENTITY(u.upline) as upline_id')
            ->from(User::class, 'u')
            ->innerJoin('u.membership', 'm')
            ->where('u.lft >= :lft AND u.rgt <= :rgt')
            ->setParameter('lft', $rootUser->getLft())
            ->setParameter('rgt', $rootUser->getRgt())
            ->orderBy('u.lft', 'ASC')
            ->getQuery()
            ->getArrayResult();

        // Index by id, then build tree
        $indexed = [];
        foreach ($rows as $row) {
            $row['__left'] = null;
            $row['__right'] = null;
            $indexed[$row['id']] = $row;
        }
        foreach ($indexed as &$row) {
            $pid = $row['upline_id'];
            if ($pid && isset($indexed[$pid])) {
                if ($row['position'] === 'Left') {
                    $indexed[$pid]['__left'] = $row['id'];
                } elseif ($row['position'] === 'Right') {
                    $indexed[$pid]['__right'] = $row['id'];
                }
            }
        }
        unset($row);

        $rootId = $rootUser->getId();

        $buildNode = function (int $nodeId, int $depth) use (&$buildNode, &$indexed, $maxDepth) {
            if (!isset($indexed[$nodeId])) return null;
            $node = $indexed[$nodeId];

            $descendantCount = (int)(($node['rgt'] - $node['lft'] - 1) / 2);
            $leftId = $node['__left'];
            $rightId = $node['__right'];

            $result = [
                'id' => $node['id'],
                'fullname' => $node['fullname'] ?? '',
                'username' => $node['username'] ?? '',
                'position' => $node['position'] ?? null,
                'activated' => $node['activated'] ?? false,
                'membership' => $node['membership_name'] ?? null,
                'descendants' => $descendantCount,
                'left_free' => $leftId === null,
                'right_free' => $rightId === null,
            ];

            if ($depth < $maxDepth) {
                $result['left'] = $leftId ? $buildNode($leftId, $depth + 1) : null;
                $result['right'] = $rightId ? $buildNode($rightId, $depth + 1) : null;
            } else {
                $result['left'] = $leftId ? $this->truncatedNode($indexed[$leftId], 'Left') : null;
                $result['right'] = $rightId ? $this->truncatedNode($indexed[$rightId], 'Right') : null;
            }

            return $result;
        };

        $tree = isset($indexed[$rootId]) ? $buildNode($rootId, 0) : null;

        return new JsonResponse($tree);
    }

    private function truncatedNode(array $node, string $position): array
    {
        $desc = isset($node['lft'], $node['rgt']) ? (int)(($node['rgt'] - $node['lft'] - 1) / 2) : 0;
        return [
            'id' => $node['id'],
            'fullname' => $node['fullname'] ?? '',
            'username' => $node['username'] ?? '',
            'activated' => $node['activated'] ?? false,
            'membership' => $node['membership_name'] ?? null,
            'position' => $position,
            'descendants' => $desc,
            'truncated' => true,
        ];
    }

    #[Route('/api/user/{id}/details', name: 'api_user_details', methods: ['GET'], options: ['expose' => true], requirements: ['id' => '\d+'])]
    public function getUserDetails(User $user): JsonResponse
    {
        /** @var User $currentUser */
        $currentUser = $this->token->getToken()->getUser();

        // Security: only show users in own network (within lft/rgt range) or if admin
        $roles = $this->token->getToken()->getRoleNames();
        $isAdmin = in_array('ROLE_JTWC_ADMIN', $roles) || in_array('ROLE_JTWC_USER_SECRET', $roles);

        $isSelf = $user->getId() === $currentUser->getId();
        $isInNetwork = $user->getLft() >= $currentUser->getLft() && $user->getRgt() <= $currentUser->getRgt();

        if (!$isAdmin && !$isSelf && !$isInNetwork) {
            return new JsonResponse(['error' => 'Access denied'], 403);
        }

        /** @var UserRepository $repository */
        $repository = $this->manager->getRepository(User::class);
        $leftTaken = $repository->validateUserPosition($user, 'Left');
        $rightTaken = $repository->validateUserPosition($user, 'Right');

        $networkSize = 0;
        if ($user->getLft() && $user->getRgt()) {
            $networkSize = (int)(($user->getRgt() - $user->getLft() - 1) / 2);
        }

        $directCount = (int) $this->manager->createQuery(
            'SELECT COUNT(u.id) FROM App\Entity\User u WHERE u.sponsor = :user'
        )->setParameter('user', $user)->getSingleScalarResult();

        $data = [
            'id' => $user->getId(),
            'fullname' => $user->getFullname(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'phone' => $user->getMobilePhone(),
            'gender' => $user->getGender(),
            'title' => $user->getTitle(),
            'city' => $user->getCity(),
            'country' => $user->getCountry(),
            'position' => $user->getPosition(),
            'activated' => $user->getActivated(),
            'state' => $user->getState(),
            'membership' => $user->getMembership() ? $user->getMembership()->getName() : null,
            'membershipCode' => $user->getMembership() ? $user->getMembership()->getCode() : null,
            'sponsor' => $user->getSponsor() ? $user->getSponsor()->getFullname() . ' (' . $user->getSponsor()->getUsername() . ')' : null,
            'upline' => $user->getUpline() ? $user->getUpline()->getFullname() . ' (' . $user->getUpline()->getUsername() . ')' : null,
            'entryDate' => $user->getEntryDate() ? $user->getEntryDate()->format('d/m/Y') : null,
            'dateActivation' => $user->getDateActivation() ? $user->getDateActivation()->format('d/m/Y') : null,
            'dateOfBirth' => $user->getDateOfBirth() ? $user->getDateOfBirth()->format('d/m/Y') : null,
            'imageName' => $user->getImageName(),
            'codeDistributor' => $user->getCodeDistributor(),
            'grade' => $user->getGrade(),
            'networkSize' => $networkSize,
            'directCount' => $directCount,
            'left_free' => !$leftTaken,
            'right_free' => !$rightTaken,
            'nextOfKin' => $user->getNextOfKin(),
        ];

        return new JsonResponse($data);
    }

    #[Route('/admin/sponsor/autocomplete', name: 'sponsor_autocomplete', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function sponsorAutocomplete(Request $request)
    {
        $names = array();
        $term = trim(strip_tags($request->get('term')));

        /**
         * @var User $sponsor
         */
        $sponsor = $this->token->getToken()->getUser();

        /**
         * @var UserRepository $repository
         */
        $repository = $this->manager->getRepository(User::class);
	  
        if ($this->token->getToken()->getRoleNames()[0] == 'ROLE_JTWC_ADMIN' || 
            $this->token->getToken()->getRoleNames()[0] == 'ROLE_JTWC_USER_SECRET') {
            
                $users = $repository->getAllMembersList($term);
                
        } else {
            $users = $repository->getSponsorOrUplineList($term, $sponsor->getLft(), $sponsor->getRgt());
        }
	  
        if ($users) {
            foreach ($users as $user) {
			  $names[] = $user->getFullname()." (".$user->getUsername().")";
			  /*if ($user->getMembership()->getCoefficent() > 1) {
                    
                }*/
            }
        }

        $response = new JsonResponse();
        $response->setData($names);

        return $response;
    }

    #[Route('/admin/upline/autocomplete', name: 'upline_autocomplete', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function uplineAutocomplete(Request $request, CheckIfUserHaveOnlyOneChild $check): JsonResponse
    {
        $names = array();
        $term = trim(strip_tags($request->get('term')));

        /**
         * @var User $sponsor
         */
        $sponsor = $this->token->getToken()->getUser();

        /**
         * @var UserRepository $repository
         */
        $repository = $this->manager->getRepository(User::class);

        $roles = $this->token->getToken()->getRoleNames();
        if (in_array('ROLE_JTWC_ADMIN', $roles) || in_array('ROLE_JTWC_USER_SECRET', $roles)) {
            $users = $repository->getAllMembersList($term);
        } else {
            $users = $repository->getSponsorOrUplineList($term, $sponsor->getLft(), $sponsor->getRgt(), true);
        }

        if ($users) {
            foreach ($users as $user) {
                $names[] = $user->getFullname()." (".$user->getUsername().")";
            }
        }

        $response = new JsonResponse();
        $response->setData($names);

        return $response;
    }

    #[Route('/admin/upline/available', name: 'upline_available_list', methods: ['GET'], options: ['expose' => true])]
    public function availableUplines(Request $request): JsonResponse
    {
        $search = trim(strip_tags($request->get('q', '')));

        /** @var User $currentUser */
        $currentUser = $this->token->getToken()->getUser();

        /** @var UserRepository $repository */
        $repository = $this->manager->getRepository(User::class);

        $roles = $this->token->getToken()->getRoleNames();
        $isAdmin = in_array('ROLE_JTWC_ADMIN', $roles) || in_array('ROLE_JTWC_USER_SECRET', $roles);

        if ($isAdmin) {
            $users = $repository->getAllActivatedMembers($search);
        } else {
            $users = $repository->getSponsorOrUplineList(
                $search ?: '%',
                $currentUser->getLft(),
                $currentUser->getRgt(),
                true
            );
        }

        $results = [];
        if ($users) {
            foreach ($users as $user) {
                $leftTaken = $repository->validateUserPosition($user, 'Left');
                $rightTaken = $repository->validateUserPosition($user, 'Right');

                if ($leftTaken && $rightTaken) {
                    continue; // Skip users with both positions taken
                }

                $positions = [];
                if (!$leftTaken) $positions[] = 'Left';
                if (!$rightTaken) $positions[] = 'Right';

                $results[] = [
                    'id' => $user->getId(),
                    'fullname' => $user->getFullname(),
                    'username' => $user->getUsername(),
                    'left_free' => !$leftTaken,
                    'right_free' => !$rightTaken,
                    'positions' => $positions,
                    'label' => $user->getFullname() . ' (' . $user->getUsername() . ')',
                ];
            }
        }

        return new JsonResponse($results);
    }

    #[Route('/users/{code}/add', name: 'add_user_quickly', methods: ['GET', 'POST'], requirements: ['code' => '[a-zA-Z]+'])]
    public function addNewUserQuickly(Membership $membership, Request $request): Response
    {
        /**
         * @var User $sponsor
         */
        $sponsor = $this->token->getToken()->getUser();

        $user = (new User())
                ->setSponsor($sponsor)
                ->setMembership($membership)
                ->setCreatedBy($sponsor);

        $this->userHandler->setEntity($user);

        return $this->userHandler->userAddFast($request);
    }

    #[Route('/new-user/update', name: 'new_user_update', methods: ['GET', 'POST'])]
    public function updateUserInfoFirstConnexion(Request $request)
    {
        /** @var User $user */
        $user = $this->token->getToken()->getUser();

        /** @var User $user */
        $user = $this->manager->getRepository(User::class)->find($user->getId());

        $template = 'back/webcontroller/user/update_user_profile.html.twig';

        return $this->userHandler->setEntity($user)->updateUserProfile($request, $template);
    }

    #[Route('/user/update/details', name: 'user_update_details', methods: ['GET', 'POST'])]
    public function updateUserInfo(Request $request)
    {
        /** @var User $user */
        $user = $this->token->getToken()->getUser();

        /** @var User $user */
        $user = $this->manager->getRepository(User::class)->find($user->getId());

        $template = 'back/webcontroller/user/update_user_info.html.twig';

        return $this->userHandler->setEntity($user)->updateUserInfo($request, $template);
    }

    #[Route('/admin/check/username', name: 'check_username', methods: ['GET', 'POST'], options: ['expose' => true])]
    public function checkUsername(Request $request): JsonResponse
    {
        $results = [];

        $data = json_decode($request->getContent(), true);

        /**
         * @var UserRepository $repository
         */
        $repository = $this->manager->getRepository(User::class);

        $users = $repository->checkUsername($data['username']);

        if (!$users) {
            $results['message'] = 'Code d\'authentification disponible';
            $results['status'] = true;
        } else {
            if ($users->getId() === (int)$data['id_user']) {
                $results['message'] = 'Code d\'authentification actuel';
            } else {
                $results['message'] = 'Code d\'authentification indisponible';
            }
            $results['status'] = false;
        }

        $results['visible'] = 'visible';

        $response = new JsonResponse();
        $response->setData($results);

        return $response;
    }

    #[Route('/recover/genealogy', name: 'recover_genealogy')]
    public function recover(RouterInterface $router, EntityManagerInterface $em)
    {
        /**
         * @var NestedTreeRepository $repo
         */
        $repo = $em->getRepository(User::class);
        if (!$repo->verify()) {
            $repo->recover();
            $em->flush();
        }
        return new RedirectResponse($router->generate('user_list'));
    }
}
