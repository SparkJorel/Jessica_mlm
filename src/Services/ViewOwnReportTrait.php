<?php

namespace App\Services;

use App\Entity\Cycle;
use App\Entity\ParameterConfig;
use App\Entity\User;
use App\Entity\SummaryCommission;
use App\Repository\SummaryCommissionRepository;
use App\Services\GetBonus;
use App\Services\GenerationalBonus;
use App\Services\IndirectBonusService;
use App\Services\BonusBinary;

/**
 * @property GetBonus $getBonus
 * @property GenerationalBonus $generationalBonus
 * @property IndirectBonusService $indirectBonusService
 * @property BonusBinary $bonusBinary
 */
trait ViewOwnReportTrait
{
    use UtilitiesRecapTrait;

    /**
     * @param User $user
     * @param Cycle $cycle
     * @return array
     */
    protected function getReportCycleClosed(User $user, Cycle $cycle)
    {
        $i = 0;
        $commissionTotal = 0;
        $commissionActif = 0;
        $commissionPassif = 0;


        /** @var SummaryCommissionRepository $repositorySummaryCommission */
        $repositorySummaryCommission = $this->manager->getRepository(SummaryCommission::class);

        /** @var SummaryCommission[]|null $summaryCommissions */
        $summaryCommissions = $repositorySummaryCommission->getReportCycleClosed([$user->getId()], $cycle);

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
            } else {
                $userReport[$summaryCommission->getReason()] = $summaryCommission->getAmount();
            }

            $i++;
        }

        $commissions = $this->computeCommission($userReport);

        $userReport['commissions'] = $commissions['total'];
        $userReport['commissionsActif'] = $commissions['actif'];
        $userReport['commissionsPassif'] = $commissions['passif'];
    
        return $userReport;
    }

    /**
     * @param User $user
     * @param Cycle $cycle
     * @param ParameterConfig $sv
     * @return array
     */
    protected function getReportCycleNotClosed(User $user, Cycle $cycle, ParameterConfig $sv)
    {
        $binaryUsers = [];
        $i = 0;
        $commissionTotal = 0;
        $commissionActif = 0;
        $commissionPassif = 0;

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

        $report = $this->bonusBinary->computeUserBonusGroup($user, $cycle, $sv);

        if (array_key_exists('gain', $report) && $report['gain'] != 0) {
            $userReport['binaire'] = $report['gain'];
        }

        $userReport['fullname'] = $user->getFullname();
        $userReport['telephone'] = $user->getMobilePhone();
        $commissions = $this->computeCommission($userReport);

        $userReport['commissions'] = $commissions['total'];
        $userReport['commissionsActif'] = $commissions['actif'];
        $userReport['commissionsPassif'] = $commissions['passif'];

        return $userReport;
    }

}