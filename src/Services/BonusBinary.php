<?php

namespace App\Services;

use App\Entity\Cycle;
use App\Entity\Membership;
use App\Entity\MembershipBonusPourcentage;
use App\Entity\MembershipSubscription;
use App\Entity\ParameterConfig;
use App\Entity\User;
use App\Entity\UserCommandPackPromo;
use App\Entity\UserCommands;
use App\Entity\UserMonthCarryOver;
use App\Entity\UserPaidBonus;
use App\Repository\CycleRepository;
use App\Repository\MembershipBonusPourcentageRepository;
use App\Repository\MembershipSubscriptionRepository;
use App\Repository\ParameterConfigRepository;
use App\Repository\UserCommandPackPromoRepository;
use App\Repository\UserCommandsRepository;
use App\Repository\UserMonthCarryOverRepository;
use App\Repository\UserPaidBonusRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class BonusBinary
{
    use UtilitiesTrait;
    use ComputeBinaryTurnOverTrait;

    /**
     * @var GetVolumeSVGenerateByCycle
     */
    private $getVolumeSVGenerateByCycle;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var GetDownlinesGenealogyView
     */
    private $view;
    /**
     * @var ExtractSVFromCommands
     */
    private $commands;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(
        GetVolumeSVGenerateByCycle $getVolumeSVGenerateByCycle,
        GetDownlinesGenealogyView $view,
        Environment $twig,
        ExtractSVFromCommands $commands,
        PaginatorInterface $paginator,
        FormFactoryInterface $formFactory,
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $manager
    )
    {
        $this->getVolumeSVGenerateByCycle = $getVolumeSVGenerateByCycle;
        $this->tokenStorage = $tokenStorage;
        $this->manager = $manager;
        $this->view = $view;
        $this->commands = $commands;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->paginator = $paginator;
    }

    /**
     * @param Request $request
     * @param Cycle|null $cycle
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getRecapBonusBinaire(Request $request, Cycle $cycle = null)
    {

        /**
         * @var ParameterConfigRepository $repositoryParam
         */
        $repositoryParam = $this->manager->getRepository(ParameterConfig::class);

        $paid = true;

        /**
         * @var UserPaidBonusRepository $repository
         */
        $repository = $this->manager->getRepository(UserPaidBonus::class);

        $bonus_binaire_cycle = [];
        $form = $this->createForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            /** @var Cycle $cycle */
            $cycle = $form->get('period')->getData();
        }

        if (!$cycle) {
            /**
             * @var CycleRepository $cycleRepository
             */
            $cycleRepository = $this->manager->getRepository(Cycle::class);

            /** @var Cycle $cycle */
            $cycle = $cycleRepository->getLastCycle();
        }

        /**
         * @var ParameterConfig $sv
         */
        $sv = $repositoryParam->valueParameter('sv', $cycle);


        $users = $this->getNetworkers();

        if (is_array($users) && !empty($users)) {
            foreach ($users as $user) {
                if ($cycle->getClosed() && $cycle->getBinarySaved()) {
                    $user_recap = $this->handleSavedCarryOver($user, $cycle);
                } else {
                    $user_recap = $this->computeUserBonusGroup($user, $cycle, $sv);
                }

                if (!empty($user_recap) && $user_recap['tl'] !== 0 && $user_recap['tr'] !== 0) {
                    $user_recap['user'] = $user;
                    $user_recap['status'] = $repository->getStatusBonusBinaireCycle(
                        $user,
                        'Binaire',
                        $cycle
                    );
                    $paid &= $user_recap['status'];
                    $bonus_binaire_cycle[] = $user_recap;
                }
            }
        }

        $bonusBinaireRecapView =
            $this
                ->twig
                ->render('back/webcontroller/bonus/view_bonus_binaire_recap_cycle.html.twig', [
                            'form' => $form->createView(),
                            'bonus_binaire_cycles' => $bonus_binaire_cycle,
                            'paid' => $paid,
                            'month' => $cycle->getEndedAt()->format('F'),
                            'year' => $cycle->getEndedAt()->format('Y'),
                            'startedAt' => $cycle->getStartedAt()->format('Y-m-d H:i:s'),
                            'endedAt' => $cycle->getEndedAt()->format('Y-m-d H:i:s'),
                            'closed' => $cycle->getClosed(),
                ]);

        return new Response($bonusBinaireRecapView);
    }

    /**
     * @param Request $request
     * @param User|null $user
     * @param Cycle|null $cycle
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getBonusBinaire(Request $request, User $user = null, Cycle $cycle = null)
    {
        /**
         * @var ParameterConfigRepository $repositoryParameterConfig
         */
        $repositoryParameterConfig = $this->manager->getRepository(ParameterConfig::class);

        /**
         * @var CycleRepository $repositoryCycle
         */
        $repositoryCycle = $this->manager->getRepository(Cycle::class);

        if (!$user) {
            /**
             * @var User $user
             */
            $user = $this->tokenStorage->getToken()->getUser();
        }

        $form = $this->createForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            /** @var Cycle $cycle */
            $cycle = $form->get('period')->getData();
        }

        if (!$cycle) {
            $cycle = $repositoryCycle->getLastCycle();
        }

        if ($user->getMembership()->getCoefficent() == 1) {
            $results = [];
            $results['pl'] = $results['al'] =  $results['tl'] = $results['pr'] = $results['ar'] = $results['tr'] = 0;
            $results['a_co'] = $results['bm'] = $results['n_co'] = $results['binaire'] = $results['gain'] = 0;
            $results['co_pos'] = '';
            $results['side'] = 'left';
            $results['sv_gain'] = 0;

            $bonusView =  $this->twig->render('back/webcontroller/bonus/view_bonus_binaire.html.twig', [
                'form' => $form->createView(),
                'results' => $results,
                'side' => $results['side'],
            ]);

        } else {

            if ($cycle->getClosed() && $cycle->getBinarySaved()) {
                $results = $this->handleSavedCarryOver($user, $cycle);
            } else {
                $sv = $repositoryParameterConfig->valueParameter('sv', $cycle);
    
                $results = $this->computeUserBonusGroup($user, $cycle, $sv);
            }
          
            /*/if (1 !== $user->getRgt() - $user->getLft()) {
                $recapActiviteReseau = $this->personalNetworkActivity($user, $cycle, $sv);
            }*/
    
            $bonusView =  $this->twig->render('back/webcontroller/bonus/view_bonus_binaire.html.twig', [
                'form' => $form->createView(),
                'results' => $results,
                'side' => $results['side'],
                //'recapActiviteReseau' => isset($recapActiviteReseau) ? $recapActiviteReseau : null,
            ]);
        }

        return new Response($bonusView);
    }

    /**
     * @param User $user
     * @param Cycle $cycle
     * @param ParameterConfig $sv
     * @return array
     */
    public function computeUserBonusGroup(User $user, Cycle $cycle, ParameterConfig $sv)
    {
        //$membershipMatching = $this->getMembershipMatchingCycle($user, $cycle);

        $results = [];
        $results['pl'] = $results['al'] =  $results['tl'] = $results['pr'] = $results['ar'] = $results['tr'] = 0;
        $results['a_co'] = $results['bm'] = $results['n_co'] = $results['binaire'] = $results['gain'] = 0;
        $results['co_pos'] = '';
        $results['side'] = '';
        $results['sv_gain'] = 0;

        if (1 === $user->getRgt() - $user->getLft() || $user->getMembership()->getCoefficent()==1) {
            return $results;
        }

        $members = [];
        $users_id['left'] = [];
        $users_id['right'] = [];

        $children = $this->getDirectChildren($user, $cycle);

        foreach ($children as $child) {
            $members[strtolower($child->getPosition())] = $this->getNetworkOfMember($child, $cycle);
        }

        /**
         * @var User[] $networks
         */
        foreach ($members as $code => $networks) {
            $volumeSVAchat = 0;

            $users_id[strtolower($code)] = array_map(function (User $network) {
                return $network->getId();
            }, $networks);

            foreach ($networks as $network) {
                $volumeSVAchat += $this->getSVPersonalPurchase($network, $cycle);
            }

            if ('l' === substr(strtolower($code), 0, 1)) {
                $results['al'] = $volumeSVAchat;
            } else {
                $results['ar'] = $volumeSVAchat;
            }
        }

        foreach ($users_id as $side => $users) {
            if (0 === count($users)) {
                if ($side === 'left') {
                    $results['pl'] = 0;
                } else {
                    $results['pr'] = 0;
                }
            } else {
                if ($cycle->getAutoSave()) {
                    $sumSVSponsoring = $this->getSumSVMembershipSubscription($cycle, $users);
                } else {
                    $subscriptions = $this->getAllSubscriptionOfCycle($cycle, $users);
                    $sumSVSponsoring = $this->getSvGroupeNetwork($subscriptions);
                }

                if ($side === 'left') {
                    $results['pl'] = $sumSVSponsoring ?? 0;
                } else {
                    $results['pr'] = $sumSVSponsoring ?? 0;
                }
            }
        }

        $results['tl'] = $results['al'] + $results['pl'];
        $results['tr'] = $results['ar'] + $results['pr'];

        /**
         * @var UserMonthCarryOverRepository $repositoryCO
         */
        $repositoryCO = $this->manager->getRepository(UserMonthCarryOver::class);

        if ($cycle->getWeekly()) {

            /** @var CycleRepository $cycleRepository */
            $cycleRepository = $this->manager->getRepository(Cycle::class);

            /** @var Cycle|null $lastCycle */
            $lastCycle = $cycleRepository->getCycleBefore($cycle);

            if ($lastCycle) {
                $old_co = $repositoryCO->getCarryOver($user, $cycle->getStartedAt()->format('F'), $cycle->getStartedAt()->format('Y'), $lastCycle->getStartedAt()->format('Y-m-d H:i:s'), $lastCycle->getEndedAt()->format('Y-m-d H:i:s'));
            } else {
                $old_co = $repositoryCO->getCarryOver($user, $cycle->getStartedAt()->format('F'), $cycle->getStartedAt()->format('Y'));
            }

            //getCarryOver(User $user, string $month, string $year, string $startedAt = null, string $endedAt = null)
        } else {
            $old_co = $repositoryCO->getCarryOver($user, $cycle->getStartedAt()->format('F'), $cycle->getStartedAt()->format('Y'));
        }

        if (!$old_co) {
            $results['a_co'] = 0;
            $side = 'left';

            $results['bm'] = $results['tl'];

            if ($results['bm']  >= $results['tr']) {
                $results['n_co'] = $results['bm'] - $results['tr'];
                $results['co_pos'] = 'left';
                $results['binaire'] = $results['tr'];
            } else {
                $results['n_co'] = $results['tr'] - $results['bm']  ;
                $results['co_pos'] = 'right';
                $results['binaire'] = $results['bm'];
            }
        } else {
            $side = strtolower($old_co->getPosition());
            $results['a_co'] = $old_co->getCo();

            if ('l' === substr($side, 0, 1)) {
                $results['bm'] = $results['tl'] + $results['a_co'];

                $results['binaire'] = $results['bm'] >= $results['tr'] ? $results['tr'] : $results['bm'];

                if ($results['bm']  >= $results['tr']) {
                    $results['n_co'] = $results['bm'] - $results['tr'];
                    $results['co_pos'] = 'left';
                } else {
                    $results['n_co'] = $results['tr'] - $results['bm']  ;
                    $results['co_pos'] = 'right';
                }
            } else {
                $results['bm'] = $results['tr'] + $results['a_co'];

                $results['binaire'] = $results['bm']  >= $results['tl'] ? $results['tl'] : $results['bm']  ;

                if ($results['bm']  >= $results['tl']) {
                    $results['n_co'] = $results['bm'] - $results['tl'];
                    $results['co_pos'] = 'right';
                } else {
                    $results['n_co'] = $results['tl'] - $results['bm']  ;
                    $results['co_pos'] = 'left';
                }
            }
        }

        $results['sv_gain'] = ($results['binaire'] * $user->getMembership()->getMembershipBonusBinairePourcent()) / 100;

        $results['gain'] = $results['sv_gain'] * $sv->getValue();
        $results['side'] = $side;
        $results['sv'] = $sv->getValue();
	  
	  /*	  		  	echo "<pre>";
	  	print_r($results);
	  echo "</pre>";
	  die;*/

        return $results;
    }

    /**
     * @param User $user
     * @param Cycle $cycle
     * @return array
     */
    public function handleSavedCarryOver(User $user, Cycle $cycle): array
    {
        $results = [];

        $results['pl'] = $results['al'] =  $results['tl'] = $results['pr'] = $results['ar'] = $results['tr'] = 0;
        $results['a_co'] = $results['bm'] = $results['n_co'] = $results['binaire'] = $results['gain'] = 0;
        $results['co_pos'] = '';
        $results['side'] = '';
        $results['sv_gain'] = 0;

        /** @var UserMonthCarryOverRepository $repository */
        $repository = $this->manager->getRepository(UserMonthCarryOver::class);

        /** @var UserMonthCarryOver|null $carryOver */
        $carryOver = $repository->getCarryOver($user, $cycle->getEndedAt()->format('F'), $cycle->getEndedAt()->format('Y'), $cycle->getStartedAt()->format('Y-m-d H:i:s'), $cycle->getEndedAt()->format('Y-m-d H:i:s'));

        if ($carryOver) {
            $results['pl'] = $carryOver->getLeftSideSponsoringSV();
            $results['al'] = $carryOver->getLeftSideAchatSV();
            $results['tl'] = $carryOver->getLeftSideTotalSV();
            $results['pr'] = $carryOver->getRightSideSponsoringSV();
            $results['ar'] = $carryOver->getRightSideAchatSV();
            $results['tr'] = $carryOver->getRightSideTotalSV();
            $results['a_co'] = $carryOver->getOldCO();
            $results['bm'] = $carryOver->getLeftOrRightSideNewTotalSV();
            $results['n_co'] = $carryOver->getCo();
            $results['binaire'] = $carryOver->getBinaire();
            $results['gain'] = $carryOver->getGain();
            $results['sv_gain'] = $carryOver->getSvGain();
            $results['co_pos'] = $carryOver->getPosition();
            $results['side'] = $carryOver->getOldCO();
        }

        return $results;
    }

    /**
     * @param User $user
     * @param Cycle $cycle
     * @return User[]|null
     */
    private function getDirectChildren(User $user, Cycle $cycle): ?array
    {
        /**
         * @var UserRepository $repository
         */
        $repository = $this->manager
                            ->getRepository(User::class);
        return $repository
                        ->getDirectChildrenOfUser($user, $cycle);
    }

    /**
     * @param User $user
     * @param Cycle $cycle
     * @return Membership|null
     */
    private function getMembershipMatchingCycle(User $user, Cycle $cycle): ?Membership
    {
        /**
         * @var MembershipSubscriptionRepository $repository
         */
        $repository = $this->manager->getRepository(MembershipSubscription::class);

        $membershipSubscription = $repository->getMembershipSubscriptionMatchingCycle($user, $cycle);
        if (!$membershipSubscription) {
            return null;
        }
        return $membershipSubscription->getMembership();
    }

    /**
     * @param Membership $membership
     * @return float|int
     */
    private function getTVC(Membership $membership)
    {
        /**
         * @var MembershipBonusPourcentageRepository $repository
         */
        $repository = $this->manager
                            ->getRepository(MembershipBonusPourcentage::class);
        /**
         * @var float $sv
         */
        $sv = $repository->bonusMembership($membership);

        if (!$sv) {
            return 0;
        }
        return $sv;
    }

    /**
     * @param User $user
     * @return User[]|null
     */
    private function getNetworkOfMember(User $user, Cycle $cycle)
    {
        /**
         * @var UserRepository $repository
         */
        $repository = $this->manager
                            ->getRepository(User::class);

        return $repository
                    ->getUserNetwork($user->getLft(), $user->getRgt(), $cycle);
    }

    private function getSVPersonalPurchase(User $user, Cycle $cycle)
    {
        $total = 0;
        /**
         * @var UserCommandsRepository $repository
         */
        $repository = $this->manager->getRepository(UserCommands::class);

        $userCommands = $repository->getAllCommandsByCycle($cycle, $user, true);
        if (!$userCommands) {
            return $total;
        }

        $svUserCommands = $this->commands->getSVFromCommands($userCommands);
        foreach ($svUserCommands as $code => $value) {
            $total += $value;
        }

        return $total;
    }

    /**
     * @param User $user
     * @param Cycle $cycle
     * @return int
     */
    private function getSVPersonalPackPurchase(User $user, Cycle $cycle)
    {
        $total = 0;

        /**
         * @var UserCommandPackPromoRepository $repository
         */
        $repository = $this->manager->getRepository(UserCommandPackPromo::class);

        $userCommandPacks = $repository->getAchatPackPromoByUserByCycle($cycle, $user);

        if (!$userCommandPacks) {
            return $total;
        }

        $svUserCommandPacks = $this->commands->getSVAchatPack($userCommandPacks);

        foreach ($svUserCommandPacks as $code => $value) {
            $total += $value;
        }

        return $total;
    }

    /**
     * @return User[]|null
     */
    private function getNetworkers()
    {
        /**
         * @var UserRepository $repository
         */
        $repository = $this->manager->getRepository(User::class);
        return $repository->getAllActivatedNetworkers();
    }

    protected function personalNetworkActivity(User $user, Cycle $cycle, ParameterConfig $sv)
    {
        /**
         * @var UserCommandsRepository $repositoryCommand
         */
        $repositoryCommand = $this->manager->getRepository(UserCommands::class);

        $recapActiviteReseau = [];
        $members = [];

        /** @var User[]|null */
        $children = $this->getDirectChildren($user, $cycle);

        if (!$children) {
            return [];
        }

        foreach ($children as $child) {
            $members[strtolower($child->getPosition())] = $this->getNetworkOfMember($child, $cycle);
        }

        /**
         * @var User[] $users
         */
        foreach ($members as $side => $users) {
            foreach ($users as $u) {
                if (1 !== $u->getRgt() - $u->getLft()) {
                    $binaire = $this->computeUserBonusGroup($u, $cycle, $sv);
                }

                $userCommands = $repositoryCommand->getAllCommandsByCycle($cycle, $user, true);

                if (!$userCommands) {
                    $svAchatPersonnel = 0;
                } else {
                    $svPersonalPurchases = $this->commands->getSVAchatPersonnel($userCommands);
                    $svAchatPersonnel = $this->commands->sommeSVAchatPersonnel($svPersonalPurchases);
                }

                if (isset($binaire)) {
                    $recapUser = [
                        'user' => $u,
                        'binaire' => $binaire,
                        'achat' => $svAchatPersonnel
                    ];
                } else {
                    $recapUser = [
                        'user' => $u,
                        'binaire' => null,
                        'achat' => $svAchatPersonnel
                    ];
                }

                $recapActiviteReseau[$side][] = $recapUser;
            }
        }

        return $recapActiviteReseau;
    }
}
