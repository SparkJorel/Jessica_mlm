<?php

namespace App\Event;

use App\Entity\PrestationService;
use Symfony\Contracts\EventDispatcher\Event;

class PrestationServiceActivatedEvent extends Event
{
    public const NAME = 'jtwc.prestation_service_activated';

    /**
     * @var PrestationService
     */
    private $prestationService;

    public function __construct(PrestationService $prestationService = null)
    {
        $this->prestationService = $prestationService;
    }

    public function getPrestationService()
    {
        return $this->prestationService;
    }
}
