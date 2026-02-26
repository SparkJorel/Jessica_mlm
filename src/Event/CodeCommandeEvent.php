<?php

namespace App\Event;

use App\Entity\UserCommands;
use Doctrine\Common\EventManager;

class CodeCommandeEvent
{
    public const NAME = 'jtwc.generate_code';
    private $command;

    private $_evm;

    public $preFooInvoked = false;

    public function __construct(EventManager $_evm)
    {
        $this->_evm = $_evm;
    }

    public function getCommand()
    {
        return $this->command;
    }
}
