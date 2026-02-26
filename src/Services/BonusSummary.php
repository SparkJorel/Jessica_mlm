<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Cycle;
use App\Entity\MembershipSubscription;
use App\Entity\ParameterConfig;
use App\Entity\User;
use App\Services\GetBonus;
use App\Services\GenerationalBonus;
use App\Services\IndirectBonusService;
use App\Services\BonusBinary;
use App\Repository\MembershipSubscriptionRepository;
use App\Repository\ParameterConfigRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BonusSummary implements BonusSummaryInterface
{
    use UtilitiesTrait, ViewOwnReportTrait;

    private $manager;
    private $tokenStorage;

    /**
     * @var GetBonus
     */
    private $getBonus;
    /**
     * @var GenerationalBonus
     */
    private $generationalBonus;

    /** @var IndirectBonusService */
    private $indirectBonusService;

    /**
     * @var BonusBinary
     */
    private $bonusBinary;

    public function __construct(
            EntityManagerInterface $manager, 
            TokenStorageInterface $tokenStorage,
            GetBonus $getBonus,
            BonusBinary $bonusBinary,
            IndirectBonusService $indirectBonusService,
            GenerationalBonus $generationalBonus
        )
    {
        $this->manager = $manager;
        $this->tokenStorage = $tokenStorage;
        $this->getBonus = $getBonus;
        $this->generationalBonus = $generationalBonus;
        $this->indirectBonusService = $indirectBonusService;
        $this->bonusBinary = $bonusBinary;
    }

    public function processAllBonuses(Cycle $cycle)
    {
        /**
         * @var User $user
         */
        $user = $this->tokenStorage->getToken()->getUser();

        /**
         * @var MembershipSubscriptionRepository
         */
        $repository = $this->manager->getRepository(MembershipSubscription::class);

        $membershipSubscription = $repository->getLastUserMembershipSubscription($user);    
        
        if ($membershipSubscription && $membershipSubscription->getMembership()->getCoefficent() < 2) {

            return [];
        }        


        if ($cycle->getClosed()) {
            $report = $this->getReportCycleClosed($user, $cycle);
        } else {
            /**
             * @var ParameterConfigRepository $repositoryCycle
             */
            $repositoryCycle = $this->manager->getRepository(ParameterConfig::class);

            $sv = $repositoryCycle->valueParameter('sv', $cycle);

            $report = $this->getReportCycleNotClosed($user, $cycle, $sv);
        }

        return $report;
    }
}