<?php

namespace App\Event;

use App\Entity\Grade;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserGradeReachedEvent extends Event
{
    /**
     * @var User
     */
    private $user;
    /**
     * @var Grade|null
     */
    private $grade;

    public function __construct(User $user = null, Grade $grade = null)
    {
        $this->user = $user;
        $this->grade = $grade;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getGrade()
    {
        return $this->grade;
    }
}
