<?php

namespace App\Event;

use App\Entity\PromoBonusSpecial;
use Symfony\Contracts\EventDispatcher\Event;

class PromotionTriggeredEvent extends Event
{
    /**
     * @var PromoBonusSpecial
     */
    private $promoBonusSpecial;

    public function __construct(PromoBonusSpecial $promoBonusSpecial)
    {
        $this->promoBonusSpecial = $promoBonusSpecial;
    }

    public function getPromoBonusSpecial()
    {
        return $this->promoBonusSpecial;
    }
}
