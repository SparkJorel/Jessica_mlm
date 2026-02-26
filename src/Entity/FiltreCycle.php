<?php

namespace App\Entity;

use DateTime;

class FiltreCycle
{
    /**
     * Cycle $period
     */
    private $period;

    /**
     * @param Cycle $period
     */
    public function setPeriod(Cycle $period): void
    {
        $this->period = $period;
    }

    /**
     * @return Cycle
     */
    public function getPeriod(): ?Cycle
    {
        return $this->period;
    }
}
