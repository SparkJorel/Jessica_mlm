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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

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

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN') or is_granted('ROLE_JTWC_USER_SECRET')")
     * @Route("/users/all", name="user_list_all", options={"expose"=true}, methods={"GET"})
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(Request $request): Response
    {
        $user = new User();
        return $this->userHandler->setEntity($user)->list($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/users/generate-code", name="users_generate_code", methods={"GET"})
     * @param GenerateCodeAllUsers $generate
     * @param RequestStack $requestStack
     * @param UrlGeneratorInterface $generator
     * @return RedirectResponse
     */
    public function generateCode(
        GenerateCodeAllUsers $generate,
        RequestStack $requestStack,
        UrlGeneratorInterface $generator
    )
    {
        $message = $generate->generateUsersCode();

        $requestStack->getSession()->getFlashBag()->add('info', $message);

        return new RedirectResponse($generator->generate('user_list_all'));
    }

    /**
     * @Route("/users", name="user_list", methods={"GET"})
     * @param GetDownlinesGenealogyView $graph
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
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

    /**
     * @Route("/users/new", name="user_new_from_known_sponsor", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
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


    /**
     * @Route("/user/{username}", name="user_show", methods={"GET"},
     * requirements={
     * "username": "\w+"
     * })
     * @param User $user
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(User $user): Response
    {
        return $this->userHandler->setEntity($user)->show();
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN') or is_granted('ROLE_JTWC_USER_SECRET')")
     * @Route("/user/{id}/activated", name="user_activated", methods={"GET"},
     * requirements={
     * "id": "\d+"
     * })
     * @param User $user
     * @return Response
     * @throws Exception
     */
    public function activateUser(User $user): Response
    {
        return $this->userHandler->redirectAfterActivation($user);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN') or is_granted('ROLE_JTWC_USER_SECRET')")
     * @Route("/users/activate", name="users_activate",
     *     methods={"GET","POST"}, options={"expose"=true}
     * )
     * @param Request $request
     * @param RouterInterface $router
     * @return RedirectResponse
     * @throws Exception
     */
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

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN') or is_granted('ROLE_JTWC_USER_SECRET')")
     * @Route("/users/{id}/edit", name="user_edit", methods={"GET","POST"},
     * requirements={"id": "\d+"}
     * )
     * @param User $user
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit(Request $request, User $user): Response
    {
        return $this->userHandler->setEntity($user)->save($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("/users/{id}/delete", name="user_delete", methods="DELETE",
     *     requirements={"id": "\d+"}
     * )
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param User $user
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, User $user): RedirectResponse
    {
        return $this
                    ->userHandler
                    ->setEntity($user)
                    ->remove($request, $csrf);
    }

    /**
     * @Route("/genealogy", name="genealogy_tree")
     * @param GetDownlinesGenealogyView $graph
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
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

    /**
     * @Route("/change/password", name="change_password", methods={"GET","POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
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

    /**
     * @Route("/change/username", name="change_username", methods={"GET","POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
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

    /**
     * @Route("/testjson", name="testjson")
     * @return JsonResponse
     */
    public function getTreeJson()
    {
        /** @var UserRepository $repository */
        $repository = $this->manager->getRepository(User::class);

        $repo = $repository->getTree();

        return new JsonResponse($repo);
    }

    /**
     * @Route("/admin/sponsor/autocomplete", methods={"GET", "POST"},  options={"expose"=true}, name="sponsor_autocomplete")
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
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

    /**
     * @Route("/admin/upline/autocomplete", methods={"GET", "POST"},  options={"expose"=true}, name="upline_autocomplete")
     *
     * @param Request $request
     * @param CheckIfUserHaveOnlyOneChild $check
     * @return JsonResponse
     */
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

        $users = $repository->getSponsorOrUplineList($term, $sponsor->getLft(), $sponsor->getRgt(), true);

        if ($users) {
            foreach ($users as $user) {
                $names[] = $user->getFullname()." (".$user->getUsername().")";
            }
        }

        $response = new JsonResponse();
        $response->setData($names);

        return $response;
    }

    /**
     * @Route("/users/{code}/add", name="add_user_quickly", requirements={
     *   "code"="[a-zA-Z]+"
     * }, methods={"GET", "POST"})
     *
     * @return Response
     */
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

    /**
     * @Route("/new-user/update", name="new_user_update", methods={"GET", "POST"})
     *
     * @return Response
     */
    public function updateUserInfoFirstConnexion(Request $request)
    {
        /** @var User $user */
        $user = $this->token->getToken()->getUser();

        /** @var User $user */
        $user = $this->manager->getRepository(User::class)->find($user->getId());

        $template = 'back/webcontroller/user/update_user_profile.html.twig';

        return $this->userHandler->setEntity($user)->updateUserProfile($request, $template);
    }

    /**
     * @Route("/user/update/details", name="user_update_details", methods={"GET", "POST"})
     *
     * @return Response
     */
    public function updateUserInfo(Request $request)
    {
        /** @var User $user */
        $user = $this->token->getToken()->getUser();

        /** @var User $user */
        $user = $this->manager->getRepository(User::class)->find($user->getId());

        $template = 'back/webcontroller/user/update_user_info.html.twig';

        return $this->userHandler->setEntity($user)->updateUserInfo($request, $template);
    }

    /**
     * @Route("/admin/check/username", methods={"GET", "POST"},
     *     options={"expose"=true}, name="check_username")
     *
     * @param Request $request
     * @return JsonResponse
     */
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

    /**
     * @Route("/recover/genealogy", name="recover_genealogy")
     * @param RouterInterface $router
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     */
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
