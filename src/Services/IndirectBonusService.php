<?php

namespace App\Services;

use App\Entity\Cycle;
use App\Entity\MembershipSubscription;
use App\Entity\CommandProducts;
use App\Entity\CommissionIndirectBonus;
use App\Entity\FiltreCycle;
use App\Entity\IndirectBonusMembership;
use App\Entity\IndirectBonusProduct;
use App\Repository\MembershipSubscriptionRepository;
use App\Entity\ParameterConfig;
use App\Repository\ParameterConfigRepository;
use App\Repository\UserRepository;
use App\Repository\CycleRepository;
use App\Entity\User;
use App\Entity\UserCommands;
use App\Form\FiltreCycleType;
use App\Repository\UserCommandsRepository;
use App\Repository\IndirectBonusProductRepository;
use App\Repository\IndirectBonusMembershipRepository;
use App\Repository\CommissionIndirectBonusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

class IndirectBonusService
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(
        EntityManagerInterface $manager,
        Environment $twig,
        FormFactoryInterface $formFactory,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->manager = $manager;
        $this->formFactory = $formFactory;
        $this->tokenStorage = $tokenStorage;
        $this->twig = $twig;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function viewUserIndirectBonus(Request $request): Response
    {
        /**
         * @var User $user
         */
        $user = $this->tokenStorage->getToken()->getUser();
        $cycle = null;

        /**
         * @var MembershipSubscriptionRepository
         */
        $repository = $this->manager->getRepository(MembershipSubscription::class);

        $form = $this->createForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            /** @var Cycle $cycle */
            $cycle = $form->get('period')->getData();
        }

        if (!$cycle) {
            /** @var CycleRepository $repositoryCycle */
            $repositoryCycle = $this->manager->getRepository(Cycle::class);
            $cycle = $repositoryCycle->getLastCycle();
        }

        $membershipSubscription = $repository->getLastUserMembershipSubscription($user);    
        
        if ($membershipSubscription && $membershipSubscription->getMembership()->getCoefficent() < 2) {

            $indirectBonusView = $this->twig->render('back/webcontroller/indirect_bonus/user_indirect_bonus.html.twig', [
                'form' => $form->createView(),
                'results' => null,
            ]);

        } else {
            
            if ($cycle->getClosed() && $cycle->getAutoSave()) {
                $results = $this->computeIndirectBonusCycleClosed($user, $cycle);
            } else {
                $results = $this->computeUserIndirectBonus($user, $cycle);
            }
    
            $indirectBonusView = $this->twig->render('back/webcontroller/indirect_bonus/user_indirect_bonus.html.twig', [
                'form' => $form->createView(),
                'results' => $results,
            ]);
        }

        return new Response($indirectBonusView);
    }

    public function viewNetWorkIndirectBonus(Request $request): Response
    {
        $form = $this->createForm();
        $form->handleRequest($request);
        $cycle = null;

        if ($form->isSubmitted()) {
            /** @var Cycle $cycle */
            $cycle = $form->get('period')->getData();
        }

        if (!$cycle) {
            /** @var CycleRepository $repositoryCycle */
            $repositoryCycle = $this->manager->getRepository(Cycle::class);
            $cycle = $repositoryCycle->getLastCycle();
        }

        $users = $this->getNetworkers();

        if ($cycle->getClosed() && $cycle->getAutoSave()) {
            $results = $this->computeNetworkIndirectBonusCycleClosed($users, $cycle);
        } else {
            $results = $this->computeNetworkIndirectBonus($users, $cycle);
        }

        $indirectBonusView = $this->twig->render('back/webcontroller/indirect_bonus/network_indirect_bonus.html.twig', [
            'form' => $form->createView(),
            'results' => $results,
        ]);

        return new Response($indirectBonusView);
    }


    /**
     * @param User[] $users
     * @param Cycle $cycle
     * @return array|null
     */
    public function computeNetworkIndirectBonusCycleClosed(array $users, Cycle $cycle): ?array
    {
        $results = [];
        $svPurchase = [];
        $totalUserIndirectBonus = 0;
        $i = $j = 0;
        $fullname = '';

        /**
         * @var CommissionIndirectBonusRepository $repositoryCommissionIndirectBonus
         */
        $repositoryCommissionIndirectBonus = $this->manager->getRepository(CommissionIndirectBonus::class);

        $user_ids = array_map(function (User $user) {
            return $user->getId();
        }, $users);

        $commissionIndirectBonuses = $repositoryCommissionIndirectBonus->getUserCommissionIndirectBonus($user_ids, $cycle);

        if (!$commissionIndirectBonuses) {
            return null;
        }

        foreach ($commissionIndirectBonuses as $commissionIndirectBonus) {
            if ($i === 0) {
                $svPurchase['nom'] = $commissionIndirectBonus->getUser()->getFullname();
                $svPurchase[$commissionIndirectBonus->getLvl()][$commissionIndirectBonus->getReason()] = $commissionIndirectBonus->getAmount();

                $i++;

                $fullname = $svPurchase['nom'];

                if ($commissionIndirectBonus->getReason() === 'total_level') {
                    $totalUserIndirectBonus += $commissionIndirectBonus->getAmount();
                }

                continue;
            }

            if ($commissionIndirectBonus->getUser()->getFullname() == $fullname) {
                $svPurchase[$commissionIndirectBonus->getLvl()][$commissionIndirectBonus->getReason()] = $commissionIndirectBonus->getAmount();

                if ($commissionIndirectBonus->getReason() === 'total_level') {
                    $totalUserIndirectBonus += $commissionIndirectBonus->getAmount();
                }
            } else {
                $j++;

                $svPurchase['total_indirect_bonus'] = $totalUserIndirectBonus;
                $svPurchase['id'] = $j;

                $results[] = $svPurchase;
                unset($svPurchase);
                unset($totalUserIndirectBonus);

                $svPurchase = [];
                $totalUserIndirectBonus = 0;

                $svPurchase['nom'] = $commissionIndirectBonus->getUser()->getFullname();
                $svPurchase[$commissionIndirectBonus->getLvl()][$commissionIndirectBonus->getReason()] = $commissionIndirectBonus->getAmount();

                $fullname = $svPurchase['nom'];

                if ($commissionIndirectBonus->getReason() === 'total_level') {
                    $totalUserIndirectBonus += $commissionIndirectBonus->getAmount();
                }
            }

            if ($i === count($commissionIndirectBonuses) - 1) {
                $j++;

                $svPurchase['total_indirect_bonus'] = $totalUserIndirectBonus;
                $svPurchase['id'] = $j;

                $results[] = $svPurchase;
            }

            $i++;
        }

        return $results;
    }

    /**
     * @param User[] $users
     * @param Cycle $cycle
     * @return array|null
     */
    public function computeNetworkIndirectBonus(array $users, Cycle $cycle): ?array
    {
        $indirectBonusReport = [];

        foreach ($users as $user) {
            $indirectBonusUser = $this->computeUserIndirectBonus($user, $cycle);

            if (!$indirectBonusUser) {
                continue;
            }

            $indirectBonusReport[] = $indirectBonusUser;
        }

        return !empty($indirectBonusReport) ? $indirectBonusReport : null;
    }

    /**
     * @param User $user
     * @param Cycle $cycle
     * @return array|null
     */
    public function computeIndirectBonusCycleClosed(User $user, Cycle $cycle): ?array
    {
        $svPurchase = [];
        $totalIndirectBonus = 0;

        /**
         * @var CommissionIndirectBonusRepository $repositoryCommissionIndirectBonus
         */
        $repositoryCommissionIndirectBonus = $this->manager->getRepository(CommissionIndirectBonus::class);

        $commissionIndirectBonuses = $repositoryCommissionIndirectBonus->getUserCommissionIndirectBonus([$user->getId()], $cycle);

        if (!$commissionIndirectBonuses) {
            return null;
        }

        foreach ($commissionIndirectBonuses as $commissionIndirectBonus) {
            $svPurchase[$commissionIndirectBonus->getLvl()][$commissionIndirectBonus->getReason()] = $commissionIndirectBonus->getAmount();
            if ('total_level' == $commissionIndirectBonus->getReason()) {
                $totalIndirectBonus += $commissionIndirectBonus->getAmount();
            }
        }

        $svPurchase['total_indirect_bonus'] = $totalIndirectBonus;
        $svPurchase['nom'] = $user->getFullname();

        return $svPurchase;
    }

    /**
     * @param User $user
     * @param Cycle $cycle
     * @return array|null
     */
    public function computeUserIndirectBonus(User $user, Cycle $cycle): ?array
    {
        $svPurchase = [];
        $totalIndirectBonus = 0;

        /**
         * @var ParameterConfigRepository $repositoryParamConfig
         */
        $repositoryParamConfig = $this->manager->getRepository(ParameterConfig::class);

        if (1 === $user->getRgt() - $user->getLft() || $user->getMembership()->getCoefficent() < 2) {
            return null;
        }

        $indirectBonus = $repositoryParamConfig->findOneBy(['name' => 'indirect_bonus', 'status' => 1]);

        if (!$indirectBonus) {
            return null;
        }

        $users = [];

        /**
         * @var UserRepository $repositoryUser
         */
        $repositoryUser = $this->manager->getRepository(User::class);

        $user_ids = [];

        for ($i = 0; $i < (int)$indirectBonus->getValue(); $i++) {
            if ($i == 0) {
                $user_ids = [$user->getId()];
            } else {
                $user_ids = array_map(function (User $user) {
                    return $user->getId();
                }, $users[$i]);
            }

            /** @var User[]|null $userGenerations */
            $userGenerations = $repositoryUser->getUsersMatchingLevel($user_ids);

            if (!$userGenerations) {
                break;
            }

            $users[$i+1] = $userGenerations;
        }

        unset($user_ids);

        if (empty($users)) {
            return null;
        }
	  
	  //dump($users);

        $purchases = $this->getPurchases($users, $cycle);

        $subscriptions = $this->getSubscriptions($users, $cycle);

        if (!$purchases && !$subscriptions) {
            return null;
        }

        if ($purchases) {
		  
            $sv = $repositoryParamConfig->findOneBy(['name' => 'sv', 'status' => 1]);

            $indirectBonusProductValues = $this->getIndirectBonusProductValues((int)$indirectBonus->getValue());
		  //dump($purchases);
		  //dump($indirectBonusProductValues);
            if ($indirectBonusProductValues) {
                foreach ($purchases as $lvl => $purchase) {
                    if (!isset($indirectBonusProductValues[$lvl])) {
                        continue;
                    }

                    $totalSV = 0;

                    foreach ($purchase as $code => $quantity) {
                        $totalSV += (isset($indirectBonusProductValues[$lvl][$code]) ? $indirectBonusProductValues[$lvl][$code] : 0) * $quantity;
                    }

                    $svPurchase[$lvl]['purchase_sv'] = $totalSV;
                    $svPurchase[$lvl]['purchase_price'] = $totalSV * $sv->getValue();
                    $svPurchase[$lvl]['total_level'] = $svPurchase[$lvl]['purchase_price'];

                    $totalIndirectBonus += $svPurchase[$lvl]['purchase_price'];
                }
            }
        }

        if ($subscriptions) {

		  //dump($subscriptions);

            $indirectBonusMembershipValues = $this->getIndirectBonusMembershipValues((int)$indirectBonus->getValue());

		  //dump($indirectBonusMembershipValues);

            if ($indirectBonusMembershipValues) {
                /** @var MembershipSubscription[] $subscriptionLevels */
                foreach ($subscriptions as $lvl => $subscriptionLevels) {
                    if (!isset($indirectBonusMembershipValues[$lvl])) {
                        continue;
                    }

                    $totalSubscription = 0;

                    foreach ($subscriptionLevels as $subscription) {
                        $totalSubscription += (isset($indirectBonusMembershipValues[$lvl][$subscription->getMembership()->getCode()]) ? $indirectBonusMembershipValues[$lvl][$subscription->getMembership()->getCode()] : 0) * ($subscription->getPrice() ? $subscription->getPrice() : $subscription->getMembership()->getMembershipCost());
                    }

                    //dump($totalSubscription);

                    $svPurchase[$lvl]['subscription_price'] = $totalSubscription;

                    if (isset($svPurchase[$lvl]['total_level'])) {
                        $svPurchase[$lvl]['total_level'] += $totalSubscription;
                    } else {
                        $svPurchase[$lvl]['total_level'] = $totalSubscription;
                    }

                    $svPurchase[$lvl]['subscription_price'] = $totalSubscription;
                    $totalIndirectBonus += $totalSubscription;
                }
            }
        }

        $svPurchase['total_indirect_bonus'] = $totalIndirectBonus;
        $svPurchase['nom'] = $user->getFullname();

        return $svPurchase;
    }

    /**
     * @param array $users
     * @param Cycle $cycle
     * @return array|null
     */
    private function getPurchases(array $users, Cycle $cycle): ?array
    {
        /** @var UserCommandsRepository $repositoryUserCommands */
        $repositoryUserCommands = $this->manager->getRepository(UserCommands::class);

        $purchases = [];

        foreach ($users as $lvl => $userGenerations) {
            $user_ids = array_map(function (User $user) {
                return $user->getId();
            }, $userGenerations);
            $purchaseLevel = $repositoryUserCommands->getAllCommandsByCycleGroupOfUsers($cycle, $user_ids, true);

            if (!$purchaseLevel) {
                continue;
            }

            $productsQuantity = [];

            foreach ($purchaseLevel as $pLevels) {
                /** @var CommandProducts $itemCommand */
                foreach ($pLevels->getProducts() as $itemCommand) {
                    if (isset($productsQuantity[$itemCommand->getProduct()->getCode()])) {
                        $productsQuantity[$itemCommand->getProduct()->getCode()] += $itemCommand->getQuantity();
                    } else {
                        $productsQuantity[$itemCommand->getProduct()->getCode()] = $itemCommand->getQuantity();
                    }
                }
            }

            $purchases[$lvl] = $productsQuantity;
        }

        return !empty($purchases) ? $purchases : null;
    }

    /**
     * @param array $users
     * @param Cycle $cycle
     * @return array|null
     */
    private function getSubscriptions(array $users, Cycle $cycle): ?array
    {
        /** @var MembershipSubscriptionRepository $repositoryMembershipSub */
        $repositoryMembershipSub = $this->manager->getRepository(MembershipSubscription::class);

        $subscriptions = [];

        foreach ($users as $lvl => $userGenerations) {
            $user_ids = array_map(function (User $user) {
                return $user->getId();
            }, $userGenerations);

            $membershipSubscriptionLevel = $repositoryMembershipSub->getMembershipSubscriptionOfSpecificLevel($cycle, $user_ids);

            if (!$membershipSubscriptionLevel) {
                continue;
            }

            $subscriptions[$lvl] = $membershipSubscriptionLevel;
        }

        return !empty($subscriptions) ? $subscriptions : null;
    }

    /**
     * @param int $indirectBonusLevel
     * @return array|null
     */
    private function getIndirectBonusProductValues(int $indirectBonusLevel): ?array
    {
        $indirectBonus = [];
        /** @var IndirectBonusProductRepository $repositoryIndirectBonusProduct */
        $repositoryIndirectBonusProduct = $this->manager->getRepository(IndirectBonusProduct::class);

        $indirectBonusProducts = $repositoryIndirectBonusProduct->findAll();

        if (!$indirectBonusProducts) {
            return null;
        }

        for ($i = 0; $i < $indirectBonusLevel; $i++) {
            $tmp = [];
            foreach ($indirectBonusProducts as $ind) {
                if ($ind->getLvl() == ($i+1)) {
                    $tmp[$ind->getProduct()->getCode()] = $ind->getValue();
                }
            }

            $indirectBonus[$i+1] = $tmp;
        }

        return $indirectBonus;
    }

    /**
     * @param int $indirectBonusLevel
     * @return array|null
     */
    private function getIndirectBonusMembershipValues(int $indirectBonusLevel): ?array
    {
        $indirectBonus = [];
        /** @var IndirectBonusMembershipRepository $repositoryIndirectBonusMembership */
        $repositoryIndirectBonusMembership = $this->manager->getRepository(IndirectBonusMembership::class);

        $indirectBonusMemberships = $repositoryIndirectBonusMembership->findAll();

        if (!$indirectBonusMemberships) {
            return null;
        }

        for ($i = 0; $i < $indirectBonusLevel; $i++) {
            $tmp = [];
            foreach ($indirectBonusMemberships as $ind) {
                if ($ind->getLvl() == ($i+1)) {
                    $tmp[$ind->getMembership()->getCode()] = $ind->getValue();
                }
            }

            $indirectBonus[$i+1] = $tmp;
        }

        return $indirectBonus;
    }

    /**
     * @return FormInterface
     */
    private function createForm(): FormInterface
    {
        $filtreCycle = new FiltreCycle();
        return $this->formFactory->create(FiltreCycleType::class, $filtreCycle);
    }

    /**
     * @return User[]
     */
    private function getNetworkers(): array
    {
        /**
         * @var UserRepository $repository
         */
        $repository = $this->manager->getRepository(User::class);
        return $repository->getAllActivatedNetworkers();
    }
}
