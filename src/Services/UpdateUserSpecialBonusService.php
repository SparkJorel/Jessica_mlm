<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;

class UpdateUserSpecialBonusService
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }
}
