<?php

namespace App\Event;

use App\Entity\UserBinaryCycle;
use Symfony\Contracts\EventDispatcher\Event;

class UserBinaryCreatedEvent extends Event
{
    public const NAME = 'jtwc.user_binary_created';

    /**
     * @var UserBinaryCycle
     */
    private $userBinaryCycle;

    public function __construct(UserBinaryCycle $userBinaryCycle = null)
    {
        $this->userBinaryCycle = $userBinaryCycle;
    }

    /**
     * @return UserBinaryCycle|null
     */
    public function getUserBinaryCycle()
    {
        return $this->userBinaryCycle;
    }
}
