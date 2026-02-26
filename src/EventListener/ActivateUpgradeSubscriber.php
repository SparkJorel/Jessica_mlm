<?php

namespace App\EventListener;

use App\Entity\MembershipSubscription;
use App\Event\ActivateUpgradeEvent;
use App\Repository\MembershipSubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ActivateUpgradeSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        // TODO: Implement getSubscribedEvents() method.
        return [
            ActivateUpgradeEvent::class => 'activateUpgrade'
        ];
    }

    public function activateUpgrade(ActivateUpgradeEvent $event): void
    {
        $user = $event->getUser();

        /**
         * @var MembershipSubscriptionRepository $repository
         */
        $repository = $this->manager->getRepository(MembershipSubscription::class);

        $membershipUpgrade = $repository->checkUpgrade($user);

        if (!$membershipUpgrade) {
            return;
        }

        /**
         * @var MembershipSubscription $lastMembershipSubscription
         */
        $lastMembershipSubscription = $repository
            ->getLastUserMembershipSubscriptionActivated(
                $user
            );

        $lastMembershipSubscription->setState(false);

        $membershipUpgrade->setState(true);
        $user->setMembership($user->getNextMembership());
        $user->setNextMembership(null);
        $user->setToUpgrade(false);
    }
}
