<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Entity\PurchaseSummary;

interface PayInInterface
{
    public function payIn(string $rcs = null);

    public function getProvider(): string;

    public function purchaseSummary(PurchaseSummary $purchaseSummary): self;

  public function setTelephone(int $telephone): self;
}
