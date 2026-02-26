<?php

namespace App\Services\ModelHandlers;

use DateTime;
use DateTimeInterface;
use Exception;
use DateTimeZone;
use App\Entity\User;
use Twig\Environment;
use Doctrine\ORM\Query;
use App\Entity\Membership;
use App\Entity\SearchUser;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use App\Form\SearchUserType;
use Twig\Error\RuntimeError;
use App\Entity\MembershipSubscription;
use App\Services\ComputeDateOperation;
use App\Form\MembershipSubscriptionType;
use App\Repository\MembershipRepository;
use App\Repository\MembershipSubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\FormFactoryInterface;
use App\Event\MembershipSubscriptionActivatedEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MembershipSubscriptionHandler extends ModelSingleEntityAbstract implements ModelInterface
{
    /**
     * @var ComputeDateOperation
     */
    private $compute;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(
        EntityManagerInterface $manager,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        Environment $twig,
        FlashBagInterface $session,
        TokenStorageInterface $tokenStorage,
        PaginatorInterface $paginator,
        ComputeDateOperation $compute,
        EventDispatcherInterface $dispatcher
    )
    {
        parent::__construct($manager, $formFactory, $router, $twig, $session);
        $this->compute = $compute;
        $this->dispatcher = $dispatcher;
        $this->tokenStorage = $tokenStorage;
        $this->paginator = $paginator;
    }

    protected function createForm(): FormInterface
    {
        // TODO: Implement createForm() method.
        return $this->formFactory
                            ->create(
                                MembershipSubscriptionType::class,
                                $this->entity,
                                ['manager' => $this->manager]
                            );
    }

    /**
     * @param Request $request
     * @param bool|null $mode
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function save(Request $request, ?bool $mode = false)
    {
        // TODO: Implement save() method.
        if ($this->entity->isNew()) {
            return $this
                ->submit(
                    $request,
                    'membership_subscription_list',
                    'back/webcontroller/membership_subscription/new.html.twig',
                    'success',
                    'Membership subscription created'
                );
        } else {
            return $this
                ->submit(
                    $request,
                    'membership_subscription_list',
                    'back/webcontroller/membership_subscription/new.html.twig',
                    'success',
                    'Membership subscription updated'
                );
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response|void
     */
    public function upgrade(Request $request)
    {
        /** @var User $createdBy */
        $createdBy = $this->tokenStorage->getToken()->getUser();

        $price = $request->request->get('price');
        $pack_inscription = $request->request->get('pack_inscription');
        $username = $request->request->get('client');

        $user_id = $this->getUserId($username);

        /** @var User $user  */
        $user = $this->manager->getRepository(User::class)->find((int)$user_id);

        /** @var Membership $membership  */
        $membership = $this->manager->getRepository(Membership::class)->find((int)$pack_inscription);

        $membershipSubscription = new MembershipSubscription();

        $membershipSubscription
                            ->setMembership($membership)
                            ->setMember($user)
                            ->setPrice($price)
                            ->setPaid(false)
                            ->setTotalSVBinaire($membership->getMembershipGroupeSV() - $user->getMembership()->getMembershipGroupeSV())
                            ->setUpgraded(true)
                            ->setCreatedBy($createdBy)
                            ->setState(false);

        $this->manager->persist($membershipSubscription);
        $this->manager->flush();

        return $this->redirectAfterSubmit(
            'membership_subscription_list',
            'info',
            'Membership subscription upgraded'
        );
    }

    /**
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @return RedirectResponse
     */
    public function upgradePack(Request $request, CsrfTokenManagerInterface $csrf)
    {
        $token = 'jtwc_membership-upgrade'.$this->getMembershipSubscription()->getMembership()->getId();

        /** @var User $createdBy */
        $createdBy = $this->tokenStorage->getToken()->getUser();

        $membershipSubscription = $this->getMembershipSubscription()->setMember($createdBy);

        if (!$this->isExistsUnpaidUpgrade($membershipSubscription) && $this->isTokenOk($csrf, $request, '_jtwc_membership_upgrade_token', $token)) {
            $membershipSubscription
                                ->setCreatedBy($createdBy)
                                ->setState(false)
                                ->setPaid(false)
                                ->setTotalSVBinaire($membershipSubscription->getMembership()->getMembershipGroupeSV() - $createdBy->getMembership()->getMembershipGroupeSV())
                                ->setUpgraded(true)
                                ->setPrice($membershipSubscription->getMembership()->getMembershipCost() - $createdBy->getMembership()->getMembershipCost())
                                ;

            $this->manager->persist($membershipSubscription);
            $this->manager->flush();

            return $this->redirectAfterSubmit(
                'membership_subscription_paid',
                'success',
                'Opération de changement de grade réussie'
            );
        } else {
            return $this->redirectAfterSubmit(
                'packs_view_all',
                'warning',
                'Echec operation de changement de pack de souscription'
            );
        }
    }

    private function isExistsUnpaidUpgrade(MembershipSubscription $membershipSubscription)
    {
        $result = $this
                    ->manager
                    ->getRepository(MembershipSubscription::class)
                    ->findOneBy([
                        'member' => $membershipSubscription->getMember(),
                        'paid' => false
                    ]);

        if ($result) {
            return true;
        }

        return false;
    }

    public function getCartSubscription()
    {
        $amount = 0;
        $i = 0;

        $code = '';

        $summary = [];

        /** @var User $createdBy */
        $createdBy = $this->tokenStorage->getToken()->getUser();

        /** @var MembershipSubscriptionRepository $membershipSubscriptionRepository */
        $membershipSubscriptionRepository = $this->manager->getRepository(MembershipSubscription::class);

        /** @var MembershipSubscription[]|null */
        $results = $membershipSubscriptionRepository->getAllMembershipSubscriptionOfUser($createdBy);

        if (!$results) {
            return null;
        }

        foreach ($results as $result) {
            if (isset($summary[$result->getMembership()->getCode()])) {
                $summary[$result->getMembership()->getCode()]['price'] += $result->getMembership()->getMembershipCost();
                $summary[$result->getMembership()->getCode()]['pack'] += 1;
            } else {
                $summary[$result->getMembership()->getCode()]['price'] = $result->getMembership()->getMembershipCost();
                $summary[$result->getMembership()->getCode()]['pack'] = 1;
            }

            if ($i == 0) {
                $code = $result->getId();
            } else {
                $code .= '_'.$result->getId();
            }

            $amount += $result->getMembership()->getMembershipCost();
            $i++;
        }

        $summary['amount'] = $amount;
        $summary['code_transaction'] = $code;

        return $summary;
    }

    /**
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrf
     * @param bool|null $mode
     * @return RedirectResponse
     */
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        // TODO: Implement remove() method.
        if ($this->isTokenValid($csrf, $request, '_jtwc_membership_subscription_token', 'jtwc_membership_subscription-delete')) {
            return $this->processRemovEntity('membership_subscription_list', 'info', 'Membership subscription deleted');
        } else {
            return $this->redirectAfterSubmit('membership_subscription_list', 'danger', 'A problem occured when processing the request!!');
        }
    }

    /**
     * @param User|null $user
     * @param Request|null $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function list(User $user = null, Request $request = null)
    {
        $criterias = [];
        $searchUSer = new SearchUser();
        $formSearch = $this->formFactory->create(SearchUserType::class, $searchUSer);

        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted()) {
            $criterias['fullname'] = $formSearch->get('fullname')->getData();
            $criterias['city'] = $formSearch->get('city')->getData();

            if ($user) {
                $mbship_subsQuery = $this->getListMembershipSubscription($user, $criterias);
            } else {
                $mbship_subsQuery = $this->getListMembershipSubscription(null, $criterias);
            }
        } else {
            if ($user) {
                $mbship_subsQuery = $this->getListMembershipSubscription($user);
            } else {
                $mbship_subsQuery = $this->getListMembershipSubscription();
            }
        }


        $mbship_subs = $this->paginator->paginate(
            $mbship_subsQuery,
            $request->query->get('page', 1),
            10
        );

        if ($mbship_subs) {
            /**
             * @var Membership|null $lastPack
             */
            $lastPack = $this->getLastPackInscription();
            /**
             * @var MembershipSubscription $mbship_sub
             */
            foreach ($mbship_subs as $mbship_sub) {
                if ($mbship_sub->getMembership()->getCoefficent() === $lastPack->getCoefficent() ||
                    $mbship_sub->getEndedAt() || !$mbship_sub->isPaid()) {
                    $mbship_sub->setUpgradable(false);
                } else {
                    $memberships = [];
                    $mbship_sub->setUpgradable(true);

                    /** @var Membership[]|null $packInscriptions */
                    $packInscriptions = $this->getMembershipUp($mbship_sub->getMembership()->getCoefficent());
                    foreach ($packInscriptions as $packInscription) {
                        $membership['id'] = $packInscription->getId();
                        $membership['name'] = $packInscription->getName();
                        array_push($memberships, $membership);
                    }
                    $mbship_sub->setMembershipUp($memberships);
                }
            }
        }

        $mbship_subsView = $this
                                ->twig
                                ->render(
                                    'back/webcontroller/membership_subscription/list.html.twig',
                                    [
                                        'mbship_subs' => $mbship_subs,
                                        'formSearch' => $formSearch->createView(),
                                    ]
                                );

        return new Response($mbship_subsView);
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show()
    {
        // TODO: Implement show() method.
        return $this->getEntityView('back/webcontroller/membership_subscription/show.html.twig');
    }

    /**
     * @param string $url_name
     * @param string $type
     * @param string $message
     * @param array|null $params
     * @return RedirectResponse|void
     * @throws Exception
     */
    protected function saveEntity(string $url_name, string $type, string $message, array $params = null)
    {
        if ($this->entity->isNew()) {
            $membershipSubscription = $this->getMembershipSubscription();

            $paidAt = new DateTime(
                "now",
                new DateTimeZone("Africa/Douala")
            );
            $user = $membershipSubscription->getMember();

            $membershipSubscription->setUpgraded(true);
            $membershipSubscription->setPaid(true);
            /** @var DateTimeInterface $dateCompute */
            $dateCompute = $this->compute->getDate($paidAt);

            if ($dateCompute > $paidAt) {
                $membershipSubscription->setPaidAt($dateCompute);
                $user->setToUpgrade(false);
                $user->setMembership($membershipSubscription->getMembership());
                $membershipSubscription->setState(true);
            } else {
                $membershipSubscription->setPaidAt($paidAt);
                $user->setNextMembership($this->getMembershipSubscription()->getMembership());
                $user->setToUpgrade(true);
                $membershipSubscription->setState(false);
            }

            $user->setServed(false);
            $this->manager->persist($this->entity);
        }

        $this->manager->flush();

        return $this->redirectAfterSubmit($url_name, $type, $message);
    }

    /**
     * @return MembershipSubscription
     */
    private function getMembershipSubscription()
    {
        /**
         * @var MembershipSubscription $membershipSubscription
         */
        $membershipSubscription = &$this->entity;
        return $membershipSubscription;
    }

    /**
     * @param MembershipSubscription $membershipSubscription
     * @throws Exception
     */
    public function activate(MembershipSubscription $membershipSubscription)
    {
        $membershipSubscription->setPaid(true);
        $membershipSubscription->getMember()->setToUpgrade(true);
        $membershipSubscription
                                ->getMember()
                                ->setNextMembership(
                                    $membershipSubscription->getMembership()
                                );

        $mbshipSubscriptionEvent = new MembershipSubscriptionActivatedEvent(
            $membershipSubscription
        );

        $this->dispatcher->dispatch($mbshipSubscriptionEvent);
        $this->manager->flush();
    }

    /**
     * @return Membership|null
     */
    public function getLastPackInscription()
    {
        /**
         * @var MembershipRepository $membershipRepository
         */
        $membershipRepository = $this->manager->getRepository(Membership::class);

        return $membershipRepository->getLastMembership();
    }

    /**
     * @param int $coeff
     * @return Membership[]|null
     */
    public function getMembershipUp(int $coeff)
    {
        /**
         * @var MembershipRepository $membershipRepository
         */
        $membershipRepository = $this->manager->getRepository(Membership::class);

        return $membershipRepository->getMembershipUp($coeff);
    }

    /**
     * @param string $username
     * @return false|string
     */
    private function getUserId(string $username)
    {
        $user_tab = explode(" ", $username);
        return substr(trim($user_tab[count($user_tab) - 1]), 1, -1);
    }

    /**
     * @param User|null $user
     * @param array|null $criterias
     * @return Query
     */
    protected function getListMembershipSubscription(User $user = null, array $criterias = null)
    {
        /**
         * @var MembershipSubscriptionRepository  $membershipSubscriptionRepository
         */
        $membershipSubscriptionRepository = $this
                                            ->manager
                                            ->getRepository(MembershipSubscription::class);

        if ($user) {
            $entities = $membershipSubscriptionRepository
                                    ->getAllMembershipSubscription($user, $criterias);
        } else {
            $entities = $membershipSubscriptionRepository
                            ->getAllMembershipSubscription(null, $criterias);
        }

        return $entities;
    }

    /**
     * @param string $template
     * @return Response
     */
    public function getSummaryCartSubscription(string $template): Response
    {
        $total = 0;
        $summary = [];

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        /** @var MembershipSubscription[]|null */
        $userSubscriptionCommands = $this->manager->getRepository(MembershipSubscription::class)->findBy(['createdBy' => $user, 'paid' => false]);

        foreach ($userSubscriptionCommands as $userSubscriptionCommand) {
            $summary[$userSubscriptionCommand->getMembership()->getName()]['quantity'] = isset($summary[$userSubscriptionCommand->getMembership()->getName()]['quantity']) ? $summary[$userSubscriptionCommand->getMembership()->getName()]['quantity'] + 1 : 1 ;
            $summary[$userSubscriptionCommand->getMembership()->getName()]['price'] = isset($summary[$userSubscriptionCommand->getMembership()->getName()]['price']) ? $summary[$userSubscriptionCommand->getMembership()->getName()]['price'] + $userSubscriptionCommand->getPrice() : $userSubscriptionCommand->getPrice() ;

            $total += $userSubscriptionCommand->getPrice();
        }

        return new Response(
            $this->twig->render($template, [
                'summaries' => $summary,
                'total' => $total
            ])
        );
    }
}
