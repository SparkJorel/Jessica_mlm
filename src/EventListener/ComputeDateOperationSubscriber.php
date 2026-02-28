<?php

namespace App\EventListener;

use App\Entity\UserCommands;
use App\Services\ComputeDateOperation;
use DateTime;
use DateTimeZone;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Exception;

class ComputeDateOperationSubscriber
{
    /**
     * @var ComputeDateOperation
     */
    private $compute;

    public function __construct(ComputeDateOperation $compute)
    {
        $this->compute = $compute;
    }

    /**
     * @param LifecycleEventArgs $event
     * @throws Exception
     */
    public function prePersist(LifecycleEventArgs $event): void
    {
        $entity = $event->getObject();
        $dateOperation = new DateTime(
            "now",
            new DateTimeZone("Africa/Douala")
        );

        if (!$entity instanceof UserCommands) {
            return ;
        }

        if ($entity->getDateCommand()) {
            /**
             * @var DateTime $dateOperation
             */
            $dateOperation = $entity->getDateCommand();
            $dateOperation = $this->compute->getDate($dateOperation);
            $entity->setDateCommand($dateOperation);
        } else {
            $dateOperation = $this->compute->getDate($dateOperation);
            $entity->setDateCommand($dateOperation);
        }

        /*if ($entity->isDistributor()) {
            $entity->setTotalDistributorPrice($entity->getTotalDistributorWithoutShippingCost());

            /** @var CommandProducts $commandProduct */
        /*    foreach ($entity->getProducts() as $commandProduct) {
                $commandProduct->setItemDistributorPrice($commandProduct->getTotalDistributorPrice());
            }
        } else {

            $entity->setTotalClientPrice($entity->getTotalClientWithoutShippingCost());

            /** @var CommandProducts $commandProduct */
        /*    foreach ($entity->getProducts() as $commandProduct) {
                $commandProduct->setItemClientPrice($commandProduct->getTotalClientPrice());
            }
        }

        $entity->setTotalSVAP($entity->processSVAPTotal());
        $entity->setTotalSVBinaire($entity->processSVBinaireTotal());*/
    }


    /**
     * @param LifecycleEventArgs $event
     * @throws Exception
     */
    public function preUpdate(LifecycleEventArgs $event): void
    {
        $entity = $event->getObject();
        $dateOperation = new DateTime(
            "now",
            new DateTimeZone("Africa/Douala")
        );

        if (!$entity instanceof UserCommands) {
            return ;
        }

        if ($entity->getDateCommand()) {
            /**
             * @var DateTime $dateOperation
             */
            $dateOperation = $entity->getDateCommand();
            $dateOperation = $this->compute->getDate($dateOperation);
            $entity->setDateCommand($dateOperation);
        } else {
            $dateOperation = $this->compute->getDate($dateOperation);
            $entity->setDateCommand($dateOperation);
        }
    }
}
