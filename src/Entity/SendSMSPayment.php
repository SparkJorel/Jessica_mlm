<?php

namespace App\Entity;

class SendSMSPayment
{
    /** @var int */
    private $telephone;

    /** @var UserCommands */
    private $userCommands;

    private $rcs;

    private $summary = [];

    public function __construct(int $telephone, UserCommands $userCommands = null, array $summary = null)
    {
        $this->telephone = $telephone;
        $this->userCommands = $userCommands;
        $this->summary = $summary;
    }

    public function getTelephone()
    {
        return $this->telephone;
    }

    public function getUserCommands()
    {
        return $this->userCommands;
    }

    /** @return array */
    public function getSummary()
    {
        return $this->summary;
    }


    public function setRcs($rcs)
    {
        $this->rcs = $rcs;
        return $this;
    }

    public function getRcs()
    {
        return $this->rcs;
    }
}
