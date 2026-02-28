<?php

namespace App\EntityListener;

use App\Entity\CommandProducts;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class CommandProductsEntityListener
{
    public function prePersist(LifecycleEventArgs $event)
    {
        $commandProducts = $event->getObject();

        if (!$commandProducts instanceof CommandProducts) {
            return;
        }

        $commandProducts->setItemSVAP($commandProducts->getTotalItemSVAP());
        $commandProducts->setItemSVBinaire($commandProducts->getTotalItemSVBinaire());

        if ($commandProducts->isDistributor()) {
            $commandProducts->setItemDistributorPrice($commandProducts->getTotalDistributorPrice());
        } else {
            $commandProducts->setItemClientPrice($commandProducts->getTotalClientPrice());
        }
    }

    public function preUpdate(LifecycleEventArgs $event)
    {
        $commandProducts = $event->getObject();

        if (!$commandProducts instanceof CommandProducts) {
            return;
        }

        $commandProducts->setItemSVAP($commandProducts->getTotalItemSVAP());
        $commandProducts->setItemSVBinaire($commandProducts->getTotalItemSVBinaire());

        if ($commandProducts->isDistributor()) {
            $commandProducts->setItemDistributorPrice($commandProducts->getTotalDistributorPrice());
        } else {
            $commandProducts->setItemClientPrice($commandProducts->getTotalClientPrice());
        }
    }
}
