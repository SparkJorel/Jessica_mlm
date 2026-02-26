<?php

namespace App\EventListener;

use App\Entity\ParameterConfig;
use App\Entity\SponsoringBonus;
use App\Event\MembershipSubscriptionActivatedEvent;
use App\Services\ComputeDateOperation;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ActivateMembershipSubscriptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var ComputeDateOperation
     */
    private $compute;

    public function __construct(EntityManagerInterface $manager, ComputeDateOperation $compute)
    {
        $this->manager = $manager;
        $this->compute = $compute;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        // TODO: Implement getSubscribedEvents() method.
        return [
            MembershipSubscriptionActivatedEvent::class => 'onMembershipSubscriptionActivated'
        ];
    }

    /**
     * @param MembershipSubscriptionActivatedEvent $event
     * @throws Exception
     */
    public function onMembershipSubscriptionActivated(MembershipSubscriptionActivatedEvent $event): void
    {
        $data = $event->getMbshipSubscription();
        $member = $data->getMember();
        $sponsor = $member->getSponsor();
        $membership = $data->getMembership();

        if (!$sponsor) {
            return;
        }

        /**
         * @var ParameterConfig $rb
         */
        $rb = $this
                    ->manager
                    ->getRepository(ParameterConfig::class)
                    ->findOneBy(
                        ['name' => 'rb', 'status' => 1]
                    );

        $value = ($data->getPrice() * $rb->getValue())/100;

        $dateActivation =  new DateTime(
            "now",
            new DateTimeZone("Africa/Douala")
        );

        /** @var DateTime $dateActivation */
        $dateActivation = $this->compute->getDate($dateActivation);

        $data->setStartedAt($this->compute->getNextStartCycle());
        $data->setPaidAt($dateActivation);

        $bonusSponsoring = new SponsoringBonus();

        $bonusSponsoring
                    ->setSponsor($sponsor)
                    ->setSponsorised($member->getFullname())
                    ->setMembership($membership->getName())
                    ->setDateActivation($dateActivation)
                    ->setValue($value)
                ;
        $this->manager->persist($bonusSponsoring);
        $this->manager->flush();
    }
}
