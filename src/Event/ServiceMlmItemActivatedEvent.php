<?php

namespace App\Event;

use App\Entity\PrestationService;

class ServiceMlmItemActivatedEvent
{
    public const NAME = 'jtwc.referral_bonus';

    /**
     * @var PrestationService
     */
    private $mlmValueService;

    public function __construct(PrestationService $mlmValueService = null)
    {
        $this->mlmValueService = $mlmValueService;
    }

    /**
     * @param PrestationService $mlmValueService
     * @return $this
     */
    public function setMlmValueService(PrestationService $mlmValueService)
    {
        $this->mlmValueService = $mlmValueService;
        return $this;
    }

    /**
     * @return PrestationService
     */
    public function getMlmValueService(): PrestationService
    {
        return $this->mlmValueService;
    }
}
