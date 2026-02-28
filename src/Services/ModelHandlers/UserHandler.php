<?php

namespace App\Services\ModelHandlers;

use App\Entity\MembershipSubscription;
use App\Entity\SearchUser;
use App\Entity\User;
use App\Entity\UserPackComposition;
use App\Entity\CompositionMembershipProductName;
use App\Event\ReferralBonusEvent;
use App\Form\AddNewUserType;
use App\Form\ChangePasswordType;
use App\Form\ChangeUsernameType;
use App\Form\SearchUserType;
use App\Form\UserProfileType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Services\ComputeDateOperation;
use App\Services\FileUploader;
use App\Services\GenerateUserDistributorCode;
use App\Services\GetUplineKnowingSponsor;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Exception;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserHandler extends ModelSingleEntityAbstract implements ModelInterface
{
    use TraitHandlers;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var UserPasswordHasherInterface
     */
    private $encoder;
    /**
     * @var GetUplineKnowingSponsor
     */
    private $upline;
    /**
     * @var PaginatorInterface
     */
    private $paginator;
    /**
     * @var FileUploader
     */
    private $fileUploader;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @var GenerateUserDistributorCode
     */
    private $codeDistributor;

    /** @var ComputeDateOperation */
    private $computeDateOperation;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(
        EntityManagerInterface $manager,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        Environment $twig,
        PaginatorInterface $paginator,
        RequestStack $requestStack,
        UserPasswordHasherInterface $encoder,
        GetUplineKnowingSponsor $upline,
        FileUploader $fileUploader,
        EventDispatcherInterface $dispatcher,
        TokenStorageInterface $tokenStorage,
        GenerateUserDistributorCode $codeDistributor,
        ComputeDateOperation $computeDateOperation,
        ParameterBagInterface $parameterBag
    )
    {
        parent::__construct($manager, $formFactory, $router, $twig, $requestStack);
        $this->dispatcher = $dispatcher;
        $this->encoder = $encoder;
        $this->upline = $upline;
        $this->paginator = $paginator;
        $this->fileUploader = $fileUploader;
        $this->parameterBag = $parameterBag;
        $this->codeDistributor = $codeDistributor ;
        $this->computeDateOperation = $computeDateOperation;
        $this->tokenStorage = $tokenStorage;
    }

    protected function createForm(): FormInterface
    {
        // TODO: Implement createForm() method.
        return $this
                    ->formFactory
                    ->create(UserType::class, $this->entity, [
                        'manager' => $this->manager
                    ]);
    }

    /**
     * @param Request $request
     * @param bool|null $mode
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function save(Request $request, ?bool $mode = false): Response
    {
        // TODO: Implement save() method.
        if ($this->entity->isNew()) {
            return $this
                        ->submit(
                            $request,
                            'user_list',
                            'back/webcontroller/user/new.html.twig',
                            'success',
                            'User created'
                        );
        } else {
            return $this
                        ->submit(
                            $request,
                            'user_list',
                            'back/webcontroller/user/new.html.twig',
                            'success',
                            'User updated'
                        );
        }
    }

    public function userAddFast(Request $request): Response
    {
        $form = $this->formFactory->create(
            AddNewUserType::class,
            $this->entity,
            ['manager' => $this->manager]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            $encoded = $this->encoder
                                ->hashPassword(
                                    $user,
                                    $user->getEmail()
                                );

            $user->setRoles(['ROLE_JTWC_USER']);
            $user->setPassword($encoded);
            $user->setUsername($user->getEmail());

            $user->setState('Inactif');

            if (!$user->getUpline()) {
                $upline = $this->upline->getUplineKnwoingIDSponsor($user);
            } else {
                $upline = $user->getUpline();
            }

            if ($upline) {
                if (!$user->getUpline()) {
                    $user->setUpline($upline);
                }
                $user->setParent($upline);
            } else {
                $user->setUpline($user->getSponsor());
                $user->setParent($user->getSponsor());
            }

            $mbshipSubscription = (new MembershipSubscription())
                    ->setMember($user)
                    ->setMembership($user->getMembership())
                    ->setState(false)
                    ->setTotalSVBinaire($user->getMembership()->getMembershipGroupeSV())
                    ->setUpgraded(false)
                    ->setPrice($user->getMembership()->getMembershipCost())
                    ->setPaid(false)
                    ->setCreatedBy($user->getCreatedBy())
            ;

            $this->manager->persist($this->entity);
            $this->manager->persist($mbshipSubscription);

            $this->manager->flush();

            if ($request->request->get('submit_and_paid')) {
                //return $this->redirectAfterSubmit('user_list', 'success', 'utilisateur créé avec succès');
            } else {
                return $this->redirectAfterSubmit('user_list', 'success', 'utilisateur créé avec succès');
            }
        }

        return new Response(
            $this->twig->render('back/webcontroller/user/quick_add.html.twig', [
                'form' => $form->createView()
            ])
        );
    }

    /**
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param bool|null $mode
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false): RedirectResponse
    {
        // TODO: Implement remove() method.
        if ($this->isTokenValid($csrf, $request, '_jtwc_user_token', 'jtwc_user-delete')) {
            return $this->processRemovEntity('user_list', 'info', 'User deleted');
        } else {
            return $this->redirectAfterSubmit('user_list', 'danger', 'A problem occured when processing the request!!');
        }
    }

    /**
     * @param Request|null $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list(Request $request = null): Response
    {
        // TODO: Implement list() method.
        $activated = true;
        $criterias = [];

        $searchUSer = new SearchUser();
        $formSearch = $this->formFactory->create(SearchUserType::class, $searchUSer);

        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted()) {
            $criterias['fullname'] = $formSearch->get('fullname')->getData();
            $criterias['city'] = $formSearch->get('city')->getData();

            /**
             * @var Query $users
             */
            $usersQuery = $this->getListUsers($criterias);
        } else {
            /**
             * @var Query $users
             */
            $usersQuery = $this->getListUsers();
        }

        $users = $this->paginator->paginate(
            $usersQuery,
            $request->query->get('page', 1),
            10
        );

        foreach ($users as $user) {
            if (!$user->getActivated()) {
                $activated = false;
            }
        }

        $usersView = $this
                        ->twig
                        ->render(
                            'back/webcontroller/user/list.html.twig',
                            [
                                'users' => $users,
                                'activated' => $activated,
                                'formSearch' => $formSearch->createView()
                            ]
                        );

        return new Response($usersView);
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show(): Response
    {
        // TODO: Implement show() method.
        return $this->getEntityView('back/webcontroller/user/show.html.twig');
    }

    /**
     * @param User $user
     * @throws Exception
     */
    public function activate(User $user): void
    {
	  
		if (!$user->isActivated()) {
			
		  	$referralBonusEvent = new ReferralBonusEvent($user);
			$user->setActivated(true);
	
			$dateActivation =  new DateTime(
				"now",
				new DateTimeZone("Africa/Douala")
			);
	
			/** @var DateTime $dateActivation */
			$dateActivation = $this->computeDateOperation->getDate($dateActivation);
	
			$user->setDateActivation($dateActivation);
	
			$user->setCodeDistributor($this->codeDistributor->generateCode($user));
	
			$this->dispatcher->dispatch($referralBonusEvent);
	
			$this->manager->flush();
			
		}
    }

    /**
     * @param User $user
     * @return RedirectResponse
     * @throws Exception
     */
    public function redirectAfterActivation(User $user): RedirectResponse
    {
        $this->activate($user);
        return $this->redirectAfterSubmit('user_list_all', 'info', 'User activated');
    }

    /**
     * @param array|null $criterias
     * @return Query
     */
    protected function getListUsers(array $criterias = null): Query
    {
        /**
         * @var UserRepository $repository
         */
        $repository = $this
                            ->manager
                            ->getRepository(User::class);

        return $repository->getAllUsers($criterias);
    }

    /**
     * @param string $url
     * @param string $type
     * @param string $message
     * @param bool|null $mode
     *
     * @return RedirectResponse
     */
    protected function processRemovEntity(string $url, string $type, string $message, ?bool $mode = false, array $params = null): RedirectResponse
    {
        $this->removeSubscription($this->entity);
        /**
         * @var NestedTreeRepository $repository
         */
        $repository = $this->manager->getRepository(get_class($this->entity));
        $repository->removeFromTree($this->entity);
        $this->manager->clear();
        if (is_array($repository->verify())) {
            $repository->recover();
            $this->manager->flush();
        }
        $this->manager->flush();

        return $this->redirectAfterSubmit($url, $type, $message);
    }

    private function removeSubscription(User $user): void
    {
        $subscriptions = $this->manager
                ->getRepository(MembershipSubscription::class)
                ->findBy([
                    'member' => $user
                ]);

        foreach ($subscriptions as $subscription) {
            $this->manager->remove($subscription);
        }
        $this->manager->flush();
    }

    protected function saveEntity(string $url_name, string $type, string $message, array $params = null): RedirectResponse
    {
        $user = $this->getUser();

        $newFileName = $this->getFileName($user);

        if ($newFileName) {
            $user->setImageName($newFileName);
        }

        if ($this->entity->isNew()) {
            $encoded = $this->encoder
                    ->hashPassword(
                        $user,
                        $user->getPassword()
                    );

            $user->setRoles(['ROLE_JTWC_USER']);
            $user->setPassword($encoded);
            $user->setServed(false);

            if (!$user->getUpline()) {
                $upline = $this->upline->getUplineKnwoingIDSponsor($user);
            } else {
                $upline = $user->getUpline();
            }

            if ($upline) {
                if (!$user->getUpline()) {
                    $user->setUpline($upline);
                }
                $user->setParent($upline);
            } else {
                $user->setUpline($user->getSponsor());
                $user->setParent($user->getSponsor());
            }

            $mbshipSubscription = (new MembershipSubscription())
                    ->setMember($user)
                    ->setMembership($user->getMembership())
                    ->setState(false)
                    ->setTotalSVBinaire($user->getMembership()->getMembershipGroupeSV())
                    ->setUpgraded(false)
                    ->setPrice($user->getMembership()->getMembershipCost())
                    ->setPaid(false)
                ;
            $this->manager->persist($this->entity);
            $this->manager->persist($mbshipSubscription);
        }

        $this->manager->flush();

        if ($params) {
            return $this->redirectAfterSubmit($url_name, $type, $message, $params);
        }
        return $this->redirectAfterSubmit($url_name, $type, $message);
    }

    public function updateUserInfo(Request $request, string $template)
    {
        $form = $this->formFactory->create(UserProfileType::class, $this->entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();
            return $this->redirectAfterSubmit('genealogy_tree', 'success', 'Informations mises à jour avec succès');
        }

        return new Response(
            $this->twig->render($template, [
                'form' => $form->createView()
            ])
        );
    }

    public function updateUserProfile(Request $request, string $template)
    {
        $form = $this->formFactory->create(UserProfileType::class, $this->entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CompositionMembershipProductName $packName */
            $packName = $form->get('pack')->getData();

            $this->getUser()->setState('Actif');
		  
			if ($packName) {
			            $userPack = (new UserPackComposition())
									  ->setUser($this->entity)
									  ->setPackName($packName)
									  ->setUpgraded(false);

            	$this->manager->persist($userPack);
			}

            $this->manager->flush();

            return $this->redirectAfterSubmit('genealogy_tree', 'success', 'Opération mise à jour réussie. Votre compte est désormais activé');
        }

        return new Response(
            $this->twig->render($template, [
                'form' => $form->createView()
            ])
        );
    }

    /**
     * @return User
     */
    private function getUser(): User
    {
        /**
         * @var User $user
         */
        $user = &$this->entity;

        return $user;
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function changePassword(Request $request)
    {
        $user = $this->getUser();
        $form = $this->formFactory->create(ChangePasswordType::class);

        $form->handleRequest($request);

        if ($this->validOldPassword($form, $user)) {
            $rawPassword = $form->get('password')->getData();
            $hashPassword = $this->encoder->hashPassword($user, $rawPassword);

            $user->setPassword($hashPassword);

            $this->manager->flush();

            $this->addFlash('info', 'Mot de passe modifié avec succès');

            return new RedirectResponse($this->router->generate('user_list'));
        }

        return new Response(
            $this->twig->render(
                'back/webcontroller/user/change_password.html.twig',
                ['form' => $form->createView()]
            )
        );
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function changeUsername(Request $request)
    {
        $user = $this->getUser();
        $form = $this->formFactory->create(ChangeUsernameType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();

            $this->addFlash('info', 'Code d\'authentification modifié avec succès');

            return new RedirectResponse($this->router->generate('user_list'));
        }

        return new Response(
            $this->twig->render(
                'back/webcontroller/user/change_username.html.twig',
                [
                    'form' => $form->createView(),
                    'id_user' => $user->getId()
                ]
            )
        );
    }

    protected function validOldPassword(FormInterface $form, User $user): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();
            if ($this->encoder->isPasswordValid($user, $oldPassword)) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }
}
