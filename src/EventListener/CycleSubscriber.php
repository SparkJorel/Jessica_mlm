<?php

namespace App\EventListener;

use App\Entity\Cycle;
use App\Repository\CycleRepository;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class CycleSubscriber implements EventSubscriber
{
    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return [
		  //Events::prePersist
        ];
    }

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
