<?php

namespace App\Services;

use App\Entity\Cycle;
//use App\Entity\MembershipSubscription;
use App\Entity\ParameterConfig;
use App\Entity\SponsoringBonus;
use App\Entity\User;
use App\Entity\UserCommands;
use App\Repository\ParameterConfigRepository;
use App\Repository\SponsoringBonusRepository;
use App\Repository\UserCommandsRepository;
use App\Repository\CycleRepository;
/*use App\Repository\UserRepository;
use App\Repository\MembershipSubscriptionRepository;*/
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class GetBonus
{
    use UtilitiesTrait;

    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var TokenStorageInterface
     */
    private $token;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var ExtractSVFromCommands
     */
    private $commands;

    public function __construct(
        EntityManagerInterface $manager,
        TokenStorageInterface $token,
        FormFactoryInterface $formFactory,
        Environment $twig,
        ExtractSVFromCommands $commands
    ) {
        $this->manager = $manager;
        $this->token = $token;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->commands = $commands;
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
    public function getBonusSponsor(Request $request, User $user = null, Cycle $cycle = null)
    {
        $form = $this->createForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $cycle = $form->get('period')->getData();
        }

        if (!$user) {
            /**
             * @var User $user
             */
            $user = $this->token->getToken()->getUser();
        }

        if (!$cycle) {
            /** @var CycleRepository $repositoryCycle */
            $repositoryCycle = $this->manager->getRepository(Cycle::class);
            $cycle = $repositoryCycle->getLastCycle();
        }

        if ($user->getMembership()->getCoefficent() < 2) {

            $bonusView =  $this->twig->render('back/webcontroller/bonus/view_bonus.html.twig', [
                'form' => $form->createView(),
                'bonusMontants' => 0,
                'total' => 0
            ]);
        } else {

            $user_recap_bsponsoring = $this->handleBonusSponsoring($user, $cycle);

            if (is_array($user_recap_bsponsoring)) {
                $bonusMontants = $user_recap_bsponsoring['recap'];
                $total = $user_recap_bsponsoring['total'];
            } else {
                $total = $user_recap_bsponsoring;
                $bonusMontants = null;
            }

            $bonusView =  $this->twig->render('back/webcontroller/bonus/view_bonus.html.twig', [
                'form' => $form->createView(),
                'bonusMontants' => $bonusMontants,
                'total' => $total
            ]);
        }

        return new Response($bonusView);
    }

    /**
     * @param User $user
     * @param Cycle $cycle
     * @return array|int
     */
    public function handleBonusSponsoring(User $user, Cycle $cycle)
    {
        $user_recap = [];
        $total = 0;
        $status = true;

        if ($user->getMembership()->getCoefficent() < 2) {
            return 0;
        }

            /**
             * @var SponsoringBonusRepository $repository
             */
            $repository = $this->manager->getRepository(SponsoringBonus::class);

        $bonusMontants = $repository->getBonusSponsoringByUserByCycle($user, $cycle);

        if ($bonusMontants) {
            foreach ($bonusMontants as $bonusMontant) {
                $total += $bonusMontant->getValue();
                $status &= $bonusMontant->isPaid();
            }

            $user_recap['recap'] = $bonusMontants;
            $user_recap['total'] = $total;
            $user_recap['status'] = $status;

            return $user_recap;
        } else {
            return $total;
        }
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
    public function getBonusPersonalPurchase(Request $request, User $user = null, Cycle $cycle = null)
    {
        /** @var float */
        $total = 0;

        if (!$user) {
            /**
             * @var User $user
             */
            $user = $this->token->getToken()->getUser();
        }

        /**
         * @var ParameterConfigRepository $repository
         */
        $repository = $this->manager->getRepository(ParameterConfig::class);

        $form = $this->createForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $cycle = $form->get('period')->getData();
        }

        if (!$cycle) {

            /** @var CycleRepository $repositoryCycle */
            $repositoryCycle = $this->manager->getRepository(Cycle::class);
            $cycle = $repositoryCycle->getLastCycle();
        }

        if ($user->getMembership()->getCoefficent() < 2) {

            $bonusView =  $this->twig->render('back/webcontroller/bonus/view_bonus_personal_purchase.html.twig', [
                'form' => $form->createView(),
                'userCommands' => null,
                'svPersonalPurchases' => null,
                'svCosts' => null,
                'total' => $total
            ]);
        } else {

            /**
             * @var ParameterConfig $sv
             */
            $sv = $repository->valueParameter('sv', $cycle);


            $results = $this->handleBonusPersonalPurchase($cycle, $user, $sv, $total);

            $bonusView =  $this->twig->render('back/webcontroller/bonus/view_bonus_personal_purchase.html.twig', [
                'form' => $form->createView(),
                'userCommands' => $results['userCommands'],
                'svPersonalPurchases' => $results['svPersonalPurchases'],
                'svCosts' => $results['svCosts'],
                'total' => $total
            ]);
        }

        return new Response($bonusView);
    }

    public function handleBonusPersonalPurchase(Cycle $cycle, User $user, ParameterConfig $sv, float &$total)
    {
       /* /**
         * @var MembershipSubscriptionRepository $membershipSubscriptionRepository
         */
        //$membershipSubscriptionRepository = $this->manager->getRepository(MembershipSubscription::class);

        /* /**
         * @var UserRepository $userRepository
         */
        //$userRepository = $this->manager->getRepository(User::class);


        $results = [];
        $svAchatCosts = [];

        if ($user->getMembership()->getCoefficent() < 2) {
            $results['svCosts'] = null;
            $results['userCommands'] = null;
            $results['svPersonalPurchases'] = null;

            return $results;
        }

        /**
         * @var UserCommandsRepository $repository
         */
        $repository = $this->manager->getRepository(UserCommands::class);

        $userCommands = $repository->obtenirLesCommandesPersonnellesEtDesPacksConsommateursDuCycle($cycle, $user);

        if (!$userCommands || empty($userCommands)) {
            $svPersonalPurchases = null;
        } else {
            $svPersonalPurchases = $this->commands->getSVAchatPersonnel($userCommands);
            $svAchatCosts = $this->aggregateSV($svPersonalPurchases, $sv, $total);
        }

        $results['svCosts'] = $svAchatCosts;
        $results['userCommands'] = $userCommands;
        $results['svPersonalPurchases'] = $svPersonalPurchases;

        // Récupération des membres de son réseau 
        /*$achatsPersonelReseauBonus = [];
        $usersNetwork = $userRepository->getUserNetwork($user->getLft(), $user->getRgt());
        $userPackConsommateurs = $membershipSubscriptionRepository->getAllMembershipSubscriptionUsingPackConsommateur();

        if ($usersNetwork && $userPackConsommateurs) {

            $userPackConsommateurIds = array_map(function (MembershipSubscription $ms) {
                return $ms->getMember()->getId();
            }, $userPackConsommateurs);

            foreach ($usersNetwork as $u) {
                if (in_array($u->getId(), $userPackConsommateurIds)) {

                    $resultats = $this->handleBonusPersonalPurchase($cycle, $u, $sv, $total);

                    array_push($achatsPersonelReseauBonus, $resultats);
                }
            }
        }

        if (!empty($achatsPersonelReseauBonus)) {
            foreach ($achatsPersonelReseauBonus as $achatPRB) {
                $results['userCommands'] = array_merge($results['userCommands'], $achatPRB['userCommands']);
                $results['svPersonalPurchases'] = array_merge($results['svPersonalPurchases'], $achatPRB['svPersonalPurchases']);
                $results['svCosts'] = array_merge($results['svCosts'], $achatPRB['svCosts']);
            }
        }*/

        return $results;
    }

    /**
     * Cette méthode convertit les SV de chaque commande en coût
     * et fait la somme des coûts
     *
     * @param array $commands
     * @param ParameterConfig $sv
     * @param float $total
     * @return array
     */
    private function aggregateSV(array $commands, ParameterConfig $sv, float &$total): array
    {
        $svCosts = [];
        $svValue = $sv->getValue();
        if ($commands) {
            foreach ($commands as $code => $value) {
                $svCosts[$code] = $value * $svValue;
                $total += $svCosts[$code];
            }
        }
        return $svCosts;
    }
}
