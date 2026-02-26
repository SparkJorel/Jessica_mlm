<?php

namespace App\Services;

use App\Entity\Cycle;
use App\Entity\FiltreCycle;
use App\Entity\ParameterConfig;
use App\Entity\User;
use App\Entity\UserCommands;
use App\Entity\UserPaidBonus;
use App\Form\FiltreCycleType;
use App\Repository\UserCommandsRepository;
use App\Repository\UserPaidBonusRepository;
use App\Repository\CycleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class GetRecapBonusSponsoringAndPersonalPurchase
{
    /**
     * @var GetBonus
     */
    private $getBonus;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var ExtractSVFromCommands
     */
    private $commands;

    public function __construct(
        GetBonus $getBonus,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $manager,
        ExtractSVFromCommands $commands,
        Environment $twig
    )
    {
        $this->getBonus = $getBonus;
        $this->formFactory = $formFactory;
        $this->manager = $manager;
        $this->twig = $twig;
        $this->commands = $commands;
    }

    /**
     * @param Request $request
     * @param Cycle|null $cycle
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getRecapSponsoring(Request $request, Cycle $cycle = null)
    {
        $paid = true;
        $bonus_sponsoring_cycle = [];
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

        $users = $this->getNetworkers();

        foreach ($users as $user) {
            $user_bonus_sponsoring = $this->getBonus->handleBonusSponsoring($user, $cycle);
            if (is_array($user_bonus_sponsoring)) {
                $user_recap = [];
                $user_recap['user'] = $user;
                $user_recap['montant_total'] = $user_bonus_sponsoring['total'];
                $paid &= $user_bonus_sponsoring['status'];
                $user_recap['status'] = $user_bonus_sponsoring['status'];
                $bonus_sponsoring_cycle[] = $user_recap;
            } else {
                continue;
            }
        }

        $bonusSponsoringRecapView =  $this
                                        ->twig
                                        ->render('back/webcontroller/bonus/view_recap_bonus_sponsoring.html.twig', [
                                            'form' => $form->createView(),
                                            'bonus_sponsoring_cycles' => $bonus_sponsoring_cycle,
                                            'paid' => $paid,
                                            'cycle' => $cycle->getId(),
                                            'closed' => $cycle->getClosed(),
                                        ]);

        return new Response($bonusSponsoringRecapView);
    }

    /**
     * @param Request $request
     * @param Cycle|null $cycle
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getRecapBonusAchatPersonal(Request $request, Cycle $cycle = null)
    {
        $paid = true;
        /**
         * @var ParameterConfig $sv
         */
        $sv = $this->manager
            ->getRepository(ParameterConfig::class)
            ->findOneBy(['name' => 'sv', 'status' => 1]);

        /**
         * @var UserPaidBonusRepository $repository
         */
        $repository = $this->manager->getRepository(UserPaidBonus::class);

        $bonus_achat_personal_cycle = [];
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

        $users = $this->getNetworkers();

        foreach ($users as $user) {
            $achatsPersonels = $this->getAchatPersonal($user, $cycle);

            if (!$achatsPersonels) {
                continue;
            }

            $recap_sv_achat = $this->commands->getSVAchatPersonnel($achatsPersonels);

            $user_recap = $this->sumSV($recap_sv_achat, $sv);

            $user_recap['user'] = $user;

            $user_recap['status'] = $repository->getStatusBonusBinaireCycle(
                $user,
                'Achat personnel',
                $cycle
            );

            $paid &= $user_recap['status'];
            $bonus_achat_personal_cycle[] = $user_recap;
        }

        $bonusAchatRecapView =  $this
                                    ->twig
                                    ->render('back/webcontroller/bonus/view_recap_bonus_achat.html.twig', [
                                        'form' => $form->createView(),
                                        'bonus_achat_personal_cycles' => $bonus_achat_personal_cycle,
                                        'paid' => $paid,
                                        'month' => $cycle->getEndedAt()->format('F'),
                                        'closed' => $cycle->getClosed(),
                                    ]);

        return new Response($bonusAchatRecapView);
    }


    /**
     * @return User[]
     */
    private function getNetworkers()
    {
        /**
         * @var UserRepository $repository
         */
        $repository = $this->manager->getRepository(User::class);
        return $repository->getAllActivatedNetworkers();
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
     * @param array $commands
     * @param ParameterConfig $sv
     * @param array|null $recap
     * @return array
     */
    private function sumSV(array $commands, ParameterConfig $sv, array $recap = null): array
    {
        if (!$recap) {
            $recap['sv_total'] = 0;
            $recap['montant_total'] = 0;
        }

        if ($commands) {
            foreach ($commands as $value) {
                $recap['sv_total'] += $value;
                $recap['montant_total'] += $value * $sv->getValue();
            }
        }
        return $recap;
    }

    /**
     * @param User $user
     * @param Cycle $cycle
     * @return UserCommands[]|null
     */
    private function getAchatPersonal(User $user, Cycle $cycle)
    {
        $recap = [];
        /**
         * @var UserCommandsRepository $repository
         */
        $repository = $this->manager->getRepository(UserCommands::class);

        /** @var UserCommands[]|null $userCommands */
        $userCommands = $repository->getAllCommandsByCycle($cycle, $user, true);

        if (!$userCommands) {
            return null;
        }

        return $userCommands;
    }
}
