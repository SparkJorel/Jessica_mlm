<?php

namespace App\Services;

use App\Entity\Cycle;

interface BonusSummaryInterface 
{
    public function processAllBonuses(Cycle $cycle);
}