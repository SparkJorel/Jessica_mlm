<?php

namespace App\Services;

use App\Entity\CommissionIndirectBonus;
use App\Entity\Cycle;
use App\Entity\ParameterConfig;
use App\Entity\User;
use App\Entity\SummaryCommission;
use App\Entity\UserMonthCarryOver;
use App\Event\ActivateUpgradeEvent;
use App\Event\ChangeGradeEvent;
use App\Repository\ParameterConfigRepository;
use App\Repository\UserRepository;
use App\Repository\SummaryCommissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CloseCycle
{
    use UtilitiesRecapTrait;

    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var BonusBinary
     */
    private $bonusBinary;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
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

    public function __construct(
        EntityManagerInterface $manager,
        EventDispatcherInterface $dispatcher,
        GetBonus $getBonus,
        BonusBinary $bonusBinary,
        IndirectBonusService $indirectBonusService,
        GenerationalBonus $generationalBonus
    )
    {
        $this->manager = $manager;
        $this->bonusBinary = $bonusBinary;
        $this->dispatcher = $dispatcher;
        $this->getBonus = $getBonus;
        $this->generationalBonus = $generationalBonus;
        $this->indirectBonusService = $indirectBonusService;
    }

    public function closeCycle(Cycle $cycle)
    {
        $binaryUsers = [];
        $users = $this->getNetworkers();
	  	$cycleStartedAt = $cycle->getStartedAt();
	  	$cycleEndedAt = $cycle->getEndedAt();

        /**
         * @var ParameterConfig $sv
         */
        $sv = $this->manager
            ->getRepository(ParameterConfig::class)
            ->findOneBy(['name' => 'sv', 'status' => 1]);


        if ($users && !empty($users)) {
            foreach ($users as $user) {

                $report = $this->bonusBinary->computeUserBonusGroup($user, $cycle, $sv);

                if (!empty($report) && array_key_exists('binaire', $report) && $report['n_co'] != 0) {
                    $changeGradeEvent = new ChangeGradeEvent(
                        $user,
                        $report['binaire']
                    );

                    $this->dispatcher->dispatch($changeGradeEvent);

                    $userCarryMonth = (new UserMonthCarryOver())
                                        ->setUser($user)
                                        ->setPosition($report['co_pos'])
                                        ->setOldPosition($report['side'])
                                        ->setCo($report['n_co'])
                                        ->setBinaire($report['binaire'])
                                        ->setLeftSideSponsoringSV($report['pl'])
                                        ->setLeftSideAchatSV($report['al'])
                                        ->setLeftSideTotalSV($report['tl'])
                                        ->setRightSideSponsoringSV($report['pr'])
                                        ->setRightSideAchatSV($report['ar'])
                                        ->setRightSideTotalSV($report['tr'])
                                        ->setOldCO($report['a_co'])
                                        ->setLeftOrRightSideNewTotalSV($report['bm'])
                                        ->setSvGain($report['sv_gain'])
                                        ->setGain($report['gain'])
                                        ->setMonth($cycle->getEndedAt()->format('F'))
                                        ->setYear($cycle->getEndedAt()->format('Y'))
                                        ->setStartedAt($cycleStartedAt)
                                        ->setEndedAt($cycleEndedAt)
                                        ;


                    $summaryCommissionBinaryBonus = (new SummaryCommission())
                                                    ->setReason('binaire')
                                                    ->setUser($user)
                                                    ->setAmount($report['gain'])
                                                    ->setMonth($cycle->getEndedAt()->format('F'))
                                                    ->setYear($cycle->getEndedAt()->format('Y'))
													->setStartedAt($cycleStartedAt)
													->setEndedAt($cycleEndedAt)
					  ;

                    $this->manager->persist($userCarryMonth);
                    $this->manager->persist($summaryCommissionBinaryBonus);
                }

                $indirectBonusUser = $this->indirectBonusService->computeUserIndirectBonus($user, $cycle);

                if ($indirectBonusUser && isset($indirectBonusUser['total_indirect_bonus'])) {
                    $summaryCommissionIndirectBonus = (new SummaryCommission())
                                ->setReason('indirect_bonus')
                                ->setUser($user)
                                ->setAmount($indirectBonusUser['total_indirect_bonus'])
                                ->setMonth($cycle->getEndedAt()->format('F'))
                                ->setYear($cycle->getEndedAt()->format('Y'))
								->setStartedAt($cycleStartedAt)
								->setEndedAt($cycleEndedAt)
					  ;

                    $this->manager->persist($summaryCommissionIndirectBonus);

                    unset($indirectBonusUser['nom']);
                    unset($indirectBonusUser['total_indirect_bonus']);

                    foreach ($indirectBonusUser as $lvl => $indirectBonusSummaryLevel) {
                        foreach ($indirectBonusSummaryLevel as $key => $value) {
                            $commissionIndirectBonus = (new CommissionIndirectBonus())
                                            ->setUser($user)
                                            ->setLvl($lvl)
                                            ->setReason($key)
                                            ->setAmount($value)
                                            ->setMonth($cycle->getEndedAt()->format('F'))
                                            ->setYear($cycle->getEndedAt()->format('Y'))
											->setStartedAt($cycleStartedAt)
											->setEndedAt($cycleEndedAt)
							  ;

                            $this->manager->persist($commissionIndirectBonus);
                        }
                    }
                }

                $generationnel = $this->generationalBonus->computeBonusGenerationnel($user, $cycle, $binaryUsers);

                if ($generationnel && isset($generationnel['gain'])) {
                    $summaryCommissionGenerationalBonus = (new SummaryCommission())
                                                    ->setReason('generationnel')
                                                    ->setUser($user)
                                                    ->setAmount($generationnel['gain'])
                                                    ->setMonth($cycle->getEndedAt()->format('F'))
                                                    ->setYear($cycle->getEndedAt()->format('Y'))
													->setStartedAt($cycleStartedAt)
													->setEndedAt($cycleEndedAt)
					  ;

                    $this->manager->persist($summaryCommissionGenerationalBonus);
                }

                $sponsoringBonus = $this
                            ->getBonus
                            ->handleBonusSponsoring($user, $cycle);

                if (is_array($sponsoringBonus)) {
                    $summaryCommissionSponsoringBonus = (new SummaryCommission())
                                                    ->setReason('parrainage')
                                                    ->setUser($user)
                                                    ->setAmount($sponsoringBonus['total'])
                                                    ->setMonth($cycle->getEndedAt()->format('F'))
                                                    ->setYear($cycle->getEndedAt()->format('Y'))
													->setStartedAt($cycleStartedAt)
													->setEndedAt($cycleEndedAt)
					  ;

                    $this->manager->persist($summaryCommissionSponsoringBonus);
                }

                $total = 0;

                $personalPurchase = $this
                    ->getBonus
                    ->handleBonusPersonalPurchase($cycle, $user, $sv, $total);

                if ($total != 0) {
                    $summaryCommissionPurchaseBonus = (new SummaryCommission())
                                                    ->setReason('achat_personal')
                                                    ->setUser($user)
                                                    ->setAmount($total)
                                                    ->setMonth($cycle->getEndedAt()->format('F'))
                                                    ->setYear($cycle->getEndedAt()->format('Y'))
													->setStartedAt($cycleStartedAt)
													->setEndedAt($cycleEndedAt)
					  ;

                    $this->manager->persist($summaryCommissionPurchaseBonus);
                }
                unset($personalPurchase);
                unset($total);

                if ($user->isToUpgrade()) {
                    $activateUpgradeEvent = new ActivateUpgradeEvent($user);
                    $this->dispatcher->dispatch($activateUpgradeEvent);
                }
            }
        }
    }

    public function viewReport(Cycle $cycle)
    {
        $reports = [];

        $users = $this->getNetworkers();

        /**
         * @var ParameterConfigRepository $repositoryCycle
         */
        $repositoryCycle = $this->manager->getRepository(ParameterConfig::class);

        $sv = $repositoryCycle->valueParameter('sv', $cycle);

        if ($users && !empty($users)) {
            if ($cycle->getClosed() && $cycle->getAutoSave()) {
                $reports = $this->getReportCycleClosed($users, $cycle);
            } else {
                $reports = $this->getReportCycleNotClosed($users, $cycle, $sv);
            }
        }

        return $reports;
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
     * @param User[] $users
     * @param Cycle $cycle
     * @param ParameterConfig $sv
     * @return array
     */
    private function getReportCycleNotClosed(array $users, Cycle $cycle, ParameterConfig $sv)
    {
        $binaryUsers = [];
        $i = 0;
        $commissionTotal = 0;
        $commissionActif = 0;
        $commissionPassif = 0;

        foreach ($users as $user) {
            $total = 0;
            $userReport = [];

            $result = $this
                        ->getBonus
                        ->handleBonusSponsoring($user, $cycle);

            if (is_array($result)) {
                $userReport['parrainage'] = $result['total'];
                unset($result);
            }

            $ac = $this
                ->getBonus
                ->handleBonusPersonalPurchase($cycle, $user, $sv, $total);

            //dump($ac);


            if ($total != 0) {
                $userReport['achat_personal'] = $total;
                unset($ac);
            }

            $generationnel = $this->generationalBonus->computeBonusGenerationnel($user, $cycle, $binaryUsers);

            if ($generationnel && isset($generationnel['gain'])) {
                $userReport['generationnel'] = $generationnel['gain'];
                unset($generationnel);
            }

            $indirectBonusUser = $this->indirectBonusService->computeUserIndirectBonus($user, $cycle);

            if ($indirectBonusUser && isset($indirectBonusUser['total_indirect_bonus'])) {
                $userReport['indirect_bonus'] = $indirectBonusUser['total_indirect_bonus'];
            }

            if (in_array($user->getUsername(), array_keys($binaryUsers))) {
                $report = $binaryUsers[$user->getUsername()];
            } else {
                if ($cycle->getClosed() && $cycle->getBinarySaved()) {
                    $report = $this->bonusBinary->handleSavedCarryOver($user, $cycle);
                } else {
                    $report = $this->bonusBinary->computeUserBonusGroup($user, $cycle, $sv);
                }
            }

            if (array_key_exists('gain', $report) && $report['gain'] != 0) {
                $userReport['binaire'] = $report['gain'];
            }

            if (!empty($userReport)) {
                $i++;
                $userReport['fullname'] = $user->getFullname();
                $userReport['telephone'] = $user->getMobilePhone();
                $commissions = $this->computeCommission($userReport);
                $userReport['commissions'] = $commissions['total'];

                $userReport['id'] = $i;
                $commissionTotal += $commissions['total'];
                $commissionActif += $commissions['actif'];
                $commissionPassif += $commissions['passif'];

                $reports[] = $userReport;
            }
        }

        $reports['actif'] = $commissionActif;
        $reports['passif'] = $commissionPassif;
        $reports['total'] = $commissionTotal;

        return $reports;
    }

    /**
     * @param User[] $users
     * @param Cycle $cycle
     * @return array
     */
    private function getReportCycleClosed(array $users, Cycle $cycle)
    {
        $i = 0;
        $commissionTotal = 0;
        $commissionActif = 0;
        $commissionPassif = 0;

        $user_ids = array_map(function (User $user) {
            return $user->getId();
        }, $users);

        /** @var SummaryCommissionRepository $repositorySummaryCommission */
        $repositorySummaryCommission = $this->manager->getRepository(SummaryCommission::class);

        /** @var SummaryCommission[]|null $summaryCommissions */
        $summaryCommissions = $repositorySummaryCommission->getReportCycleClosed($user_ids, $cycle);

        if (!$summaryCommissions) {
            return [];
        }

        $fullname = '';
        $i = 0;
        $j = 0;
        $userReport = [];
        $reports = [];

        foreach ($summaryCommissions as $summaryCommission) {
            if ($i == 0) {
                $userReport['fullname'] = $summaryCommission->getUser()->getFullname();
                $userReport['telephone'] = $summaryCommission->getUser()->getMobilePhone();
                $userReport[$summaryCommission->getReason()] = $summaryCommission->getAmount();
                $i++;

                $fullname = $userReport['fullname'];

                continue;
            }

            if ($summaryCommission->getUser()->getFullname() === $fullname) {
                $userReport[$summaryCommission->getReason()] = $summaryCommission->getAmount();
            } else {
                $commissions = $this->computeCommission($userReport);
                
                if ($commissions['total'] > 0) {
                    $j++;

                    $userReport['commissions'] = $commissions['total'];
                    $userReport['id'] = $j;
                    $commissionTotal += $commissions['total'];
                    $commissionActif += $commissions['actif'];
                    $commissionPassif += $commissions['passif'];
    
                    $reports[] = $userReport;
    
                }

                unset($userReport);
                $userReport = [];

                $userReport['fullname'] = $summaryCommission->getUser()->getFullname();
                $userReport['telephone'] = $summaryCommission->getUser()->getMobilePhone();
                $userReport[$summaryCommission->getReason()] = $summaryCommission->getAmount();

                $fullname = $userReport['fullname'];
            }

            if ($i === count($summaryCommissions) - 1) {
           
                $commissions = $this->computeCommission($userReport);

                if ($commissions['total'] > 0) {
                    $j++;

                    $userReport['commissions'] = $commissions['total'];
                    $userReport['id'] = $j;
                    $commissionTotal += $commissions['total'];
                    $commissionActif += $commissions['actif'];
                    $commissionPassif += $commissions['passif'];
    
                    $reports[] = $userReport;
    
                }

                unset($userReport);
            }

            $i++;
        }

        $reports['actif'] = $commissionActif;
        $reports['passif'] = $commissionPassif;
        $reports['total'] = $commissionTotal;

        return $reports;
    }
}
