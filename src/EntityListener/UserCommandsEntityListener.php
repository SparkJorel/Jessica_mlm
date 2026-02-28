<?php

namespace App\EntityListener;

use App\Entity\UserCommands;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UserCommandsEntityListener
{
    public function prePersist(LifecycleEventArgs $event)
    {
        $userCommands = $event->getObject();

        if (!$userCommands instanceof UserCommands) {
            return;
        }

        if ($userCommands->isDistributor()) {
            $userCommands->setTotalDistributorPrice($userCommands->getTotalDistributorWithoutShippingCost());
        } else {
            $userCommands->setTotalClientPrice($userCommands->getTotalClientWithoutShippingCost());
        }

        $userCommands->setTotalSVAP($userCommands->processSVAPTotal());
        $userCommands->setTotalSVBinaire($userCommands->processSVBinaireTotal());
    }

    public function preUpdate(LifecycleEventArgs $event)
    {
        $userCommands = $event->getObject();

        if (!$userCommands instanceof UserCommands) {
            return;
        }

        if ($userCommands->isDistributor()) {
            $userCommands->setTotalDistributorPrice($userCommands->getTotalDistributorWithoutShippingCost());
        } else {
            $userCommands->setTotalClientPrice($userCommands->getTotalClientWithoutShippingCost());
        }

        $userCommands->setTotalSVAP($userCommands->processSVAPTotal());
        $userCommands->setTotalSVBinaire($userCommands->processSVBinaireTotal());
    }
}
