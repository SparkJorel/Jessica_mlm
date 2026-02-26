<?php

namespace App\Event;

use App\Entity\Cycle;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class ReferralBonusEvent extends Event
{
    public const NAME = 'jtwc.referral_bonus';
    private $user;

    public function __construct(User $user = null)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
    public function setUser(User $user = null)
    {
        $this->user = $user;
        return $this;
    }
}
