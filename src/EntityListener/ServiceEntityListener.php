<?php

namespace App\EntityListener;

use App\Entity\Service;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Exception;

class ServiceEntityListener
{
    /**
     * @param Service $service
     * @param LifecycleEventArgs $event
     * @throws Exception
     */
    public function prePersist(Service $service, LifecycleEventArgs $event)
    {
        $service->computeSlug();
        $service->setStatus(true);
        $service
            ->setRecordedAt(
                new DateTime(
                    "now",
                    new DateTimeZone("Africa/Douala")
                )
            );
    }

    public function preUpdate(Service $service, LifecycleEventArgs $event)
    {
        $service->computeSlug();
    }
}
