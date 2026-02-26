<?php

namespace App\EventListener;

use App\Entity\ParameterConfig;
use App\Entity\SponsoringBonus;
use App\Entity\User;
use App\Event\ReferralBonusEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReferralBonusSubscriber implements EventSubscriberInterface
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
        // TODO: Implement getSubscribedEvents() method.
        return [
            ReferralBonusEvent::class => 'onUserActivate'
        ];
    }

    /**
     * @param ReferralBonusEvent $event
     */
    public function onUserActivate(ReferralBonusEvent $event): void
    {
        $data = $event->getUser();
        $sponsor = $data->getSponsor();

        if (!$data->getSponsor()) {
            return;
        }

        $bonusSponsoring = new SponsoringBonus();

        /**
         * @var ParameterConfig $rb
         */
        $rb = $this
                    ->manager
                    ->getRepository(ParameterConfig::class)
                    ->findOneBy(
                        ['name' => 'rb', 'status' => 1]
                    );

        $membership = $data->getMembership();

        $value = ($membership->getMembershipCost() * $rb->getValue())/100;

        if (!$this->manager->contains($sponsor)) {
            $sponsor = $this
                        ->manager
                        ->getRepository(User::class)->find($sponsor->getId());
        }

        $bonusSponsoring
                        ->setSponsor($sponsor)
                        ->setSponsorised($data->getFullname())
                        ->setMembership($data->getMembership()->getName())
                        ->setDateActivation($data->getDateActivation())
                        ->setValue($value);

        $this->manager->persist($bonusSponsoring);
        //$this->manager->flush();
    }
}
