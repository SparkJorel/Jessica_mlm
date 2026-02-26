<?php

namespace App\Event;

use App\Entity\MembershipSubscription;
use Symfony\Contracts\EventDispatcher\Event;

class MembershipSubscriptionActivatedEvent extends Event
{
    public const NAME = 'jtwc.membership_subscription';
    private $mbshipSubscription;

    public function __construct(MembershipSubscription $mbshipSubscription = null)
    {
        $this->mbshipSubscription = $mbshipSubscription;
    }

    public function getMbshipSubscription()
    {
        return $this->mbshipSubscription;
    }

    public function setMbshipSubscription(MembershipSubscription $usembshipSubscription = null)
    {
        $this->mbshipSubscription = $usembshipSubscription;
        return $this;
    }
}
