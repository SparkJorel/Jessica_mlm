<?php

namespace App\EventListener;

use DateTime;
use Exception;
use App\Entity\Cycle;
use Doctrine\ORM\Events;
use App\Repository\CycleRepository;
use Doctrine\Common\EventSubscriber;
use App\Entity\MembershipSubscription;
use App\Repository\MembershipSubscriptionRepository;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class CloseMembershipSubscriptionSubscriber implements EventSubscriber
{
    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws Exception
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $manager = $args->getObjectManager();

        if (!$entity instanceof MembershipSubscription ||
            ($entity instanceof MembershipSubscription && !$entity->isUpgraded())) {
            return;
        }

        /**
         * @var MembershipSubscriptionRepository $repository
         */
        $repository = $manager->getRepository(MembershipSubscription::class);

        /**
         * @var MembershipSubscription|null $membershipSubscription
         */
        $membershipSubscription = $repository
                                    ->getLastUserMembershipSubscription(
                                        $entity->getMember()
                                    );

        if (!$membershipSubscription) {
            return ;
        }

        /**
         * @var CycleRepository $cycleRepository
         */
        $cycleRepository = $manager->getRepository(Cycle::class);

        /**
         * @var Cycle|null $cycle
         */
        $cycle = $cycleRepository->getLastCycle();

        if (!$cycle) {
            return;
        }

        /**
         * @var DateTime $endedAt
         */
        $endedAt = $cycle->getEndedAt();

        /**
         * @var DateTime $startedAt
         */
        $startedAt = $cycle->getStartedAt();

        $entity->setStartedAt($startedAt);

        if (!$entity->getState()) {
            $membershipSubscription->setEndedAt($endedAt);
        } else {
            $membershipSubscription->setState(false)->setEndedAt($endedAt);
        }
    }
}
