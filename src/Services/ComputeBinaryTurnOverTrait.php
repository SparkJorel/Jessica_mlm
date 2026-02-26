<?php

namespace App\Services;

use App\Entity\Cycle;
use App\Entity\Membership;
use App\Entity\MembershipCost;
use App\Entity\MembershipSubscription;
use App\Entity\MembershipSV;
use App\Repository\MembershipSubscriptionRepository;
use App\Repository\MembershipRepository;
use App\Repository\MembershipSVRepository;
use App\Repository\MembershipCostRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 *
 * Common features needed in handling of binary and turnover services
 *
 * @property EntityManagerInterface $manager
 * Trait ComputeBinaryTurnOverTrait
 * @package App\Services
 */
trait ComputeBinaryTurnOverTrait
{
    /**
     * @param Cycle $cycle
     * @param array|null $users
     * @return MembershipSubscription[]|null
     */
    protected function getAllSubscriptionOfCycle(Cycle $cycle, array $users = null)
    {
        /**
         * @var MembershipSubscriptionRepository $repository
         */
        $repository =  $this
                            ->manager
                            ->getRepository(MembershipSubscription::class);

        if ($users && is_array($users)) {
            return $repository->getAllMemberSubscriptionOfCycle($cycle, $users);
        } else {
            return $repository->getAllMemberSubscriptionOfCycle($cycle);
        }
    }

    protected function getSumSVMembershipSubscription(Cycle $cycle, array $users = null)
    {
        /**
         * @var MembershipSubscriptionRepository $repository
         */
        $repository =  $this
                            ->manager
                            ->getRepository(MembershipSubscription::class);

        if ($users && is_array($users)) {
            return $repository->getSumSVMatchingMemberSubscriptionOfCycle($cycle, $users);
        } else {
            return $repository->getSumSVMatchingMemberSubscriptionOfCycle($cycle);
        }
    }


    protected function getSumPriceMembershipSubscription(Cycle $cycle, array $users = null)
    {
        /**
         * @var MembershipSubscriptionRepository $repository
         */
        $repository =  $this
                            ->manager
                            ->getRepository(MembershipSubscription::class);

        if ($users && is_array($users)) {
            return $repository->getSumPricesMatchingMemberSubscriptionOfCycle($cycle, $users);
        } else {
            return $repository->getSumPricesMatchingMemberSubscriptionOfCycle($cycle);
        }
    }


    /**
     * @param MembershipSubscription[] $memberSubscriptions
     * @return float|int
     */
    protected function getSvGroupeNetwork(array $memberSubscriptions)
    {
        $total = 0;
        foreach ($memberSubscriptions as $subscription) {
            if ($subscription->isUpgraded()) {
                $previousPack = $this->getPreviousMembership($subscription->getMembership());
                if ($previousPack) {
                    $total += $subscription->getMembership()->getMembershipGroupeSV() - $previousPack->getMembershipGroupeSV();
                }
            } else {
                $total += $subscription->getMembership()->getMembershipGroupeSV();
            }
        }

        return $total;
    }

    /**
     * @param MembershipSubscription[] $memberSubscriptions
     * @return float|int
     */
    protected function getSponsorshipCostOfCycle(array $memberSubscriptions)
    {
        $total = 0;
        foreach ($memberSubscriptions as $subscription) {
            if ($subscription->isUpgraded()) {
                $previousPack = $this->getPreviousMembership($subscription->getMembership());
                if ($previousPack) {
                    $total += $subscription->getMembership()->getMembershipCost() - $previousPack->getMembershipCost();
                }
            } else {
                $total += $subscription->getMembership()->getMembershipCost();
            }
        }

        return $total;
    }

    /**
     * @param Membership $membership
     * @return Membership
     */
    protected function getPreviousMembership(Membership $membership)
    {
        /** @var MembershipRepository $repositoryMembership */
        $repositoryMembership = $this->manager->getRepository(Membership::class);

        /**
         * @var Membership $pack
         */
        $pack = $repositoryMembership
                                ->getPreviousMembership($membership->getCoefficent());

        return $pack;
    }

    /**
     * @param Membership $membership
     * @param Cycle $cycle
     * @return float|int
     */
    protected function getSVGroupe(Membership $membership, Cycle $cycle)
    {

        /** @var MembershipSVRepository $repositoryMembershipSV */
        $repositoryMembershipSV = $this->manager->getRepository(MembershipSV::class);

        /**
         * @var MembershipSV $sv
         */
        $sv = $repositoryMembershipSV
                            ->membershipSV($membership, $cycle);

        if (!$sv) {
            return 0;
        }
        return $sv->getSvGroupe();
    }

    /**
     * @param Membership $membership
     * @param Cycle $cycle
     * @return float|int
     */
    protected function getCostMembership(Membership $membership, Cycle $cycle)
    {
        /** @var MembershipCostRepository $repositoryMembershipCost */
        $repositoryMembershipCost = $this->manager->getRepository(MembershipCost::class);

        /**
         * @var MembershipCost $cost
         */
        $cost = $repositoryMembershipCost->membershipCost($membership, $cycle);

        if (!$cost) {
            return 0;
        }

        return $cost->getValue();
    }

    /**
     * @param $new
     * @param $old
     * @return float|int
     */
    protected function getDiffWhenUpgraded($new, $old)
    {
        return $new - $old;
    }
}
