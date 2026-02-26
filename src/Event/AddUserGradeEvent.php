<?php

namespace App\Event;

use App\Entity\Cycle;
use App\Entity\UserGrade;
use Symfony\Contracts\EventDispatcher\Event;

class AddUserGradeEvent extends Event
{
    public const NAME = 'jtwc.add_user_grade';

    /**
     * @var UserGrade
     */
    private $userGrade;

    /**
     * @var Cycle
     */
    private $cycle;

    public function __construct(UserGrade $userGrade = null, Cycle $cycle = null)
    {
        $this->cycle = $cycle;
        $this->userGrade = $userGrade;
    }

    public function getUserGrade()
    {
        return  $this->userGrade;
    }

    public function getCycle()
    {
        return $this->cycle;
    }
}
