<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class ChangeGradeEvent extends Event
{
    public const NAME = 'jtwc.change_grade';
    /**
     * @var User
     */
    private $user;
    /**
     * @var float
     */
    private $binaire;

    public function __construct(User $user = null, float $binaire = null)
    {
        $this->user = $user;
        $this->binaire = $binaire;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return float
     */
    public function getBinaire(): float
    {
        return $this->binaire;
    }
}
