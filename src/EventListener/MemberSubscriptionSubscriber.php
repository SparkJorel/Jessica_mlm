<?php

namespace App\EventListener;

use DateTime;
use App\Entity\Cycle;
use App\Entity\User;
use App\Event\ReferralBonusEvent;
use App\Repository\CycleRepository;
use App\Repository\MembershipSubscriptionRepository;
use App\Entity\MembershipSubscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MemberSubscriptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ReferralBonusEvent::class => ['onUserActivated', 100]
        ];
    }

    public function onUserActivated(ReferralBonusEvent $event): void
    {
        /** @var User $data */
        $data = $event->getUser();

        /**
         * @var MembershipSubscriptionRepository $repository
         */
        $repository = $this->manager->getRepository(MembershipSubscription::class);

        /**
         * @var CycleRepository $cycleRepository
         */
        $cycleRepository = $this->manager->getRepository(Cycle::class);

        $membershipSubscription = $repository->getLastUserMembershipSubscription($data);

        /**
         * @var Cycle|null $cycle
         */
        $cycle = $cycleRepository->getLastCycle();

        if (!$cycle) {
            return;
        }

        $startedAt = $cycle->getStartedAt();
        /** @var $startedAt DateTime */

        if (!$membershipSubscription) {
            $membershipSubscription =
                    (new MembershipSubscription())
                        ->setMembership(
                            $data->getMembership()
                        )
                        ->setMember($data)
                        ->setStartedAt($startedAt)
                        ->setState(true)
                        ->setPaid(true)
                        ->setPrice($data->getMembership()->getMembershipCost())
                        ->setTotalSVBinaire($data->getMembership()->getMembershipGroupeSV())
                        ->setPaidAt($data->getDateActivation());

            $this->manager->persist($membershipSubscription);
        } else {
            $membershipSubscription
                                ->setPaidAt($data->getDateActivation())
                                ->setPaid(true)
                                ->setStartedAt($startedAt)
                                ->setState(true)
            ;
        }
    }
}
