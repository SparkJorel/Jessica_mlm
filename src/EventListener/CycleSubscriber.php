<?php

namespace App\EventListener;

use App\Entity\Cycle;
use App\Repository\CycleRepository;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class CycleSubscriber
{
  /*public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $manager = $args->getObjectManager();

        if (!$entity instanceof Cycle) {
            return ;
        }

        /**
         * @var CycleRepository $repository
         */
  /*$repository = $manager->getRepository(Cycle::class);

        $cycle = $repository->getLastCycle();

        if (!$cycle) {
            return;
        }

        $cycle->setActive(false);
	  //$manager->flush();
    }*/
}
