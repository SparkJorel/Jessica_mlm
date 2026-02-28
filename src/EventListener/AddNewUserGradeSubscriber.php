<?php

namespace App\EventListener;

use App\Entity\Cycle;
use App\Entity\UserGrade;
use App\Repository\CycleRepository;
use App\Repository\UserGradeRepository;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class AddNewUserGradeSubscriber implements EventSubscriber
{
    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents():array
    {
        return [
            Events::prePersist
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $manager = $args->getObjectManager();
        $entity = $args->getObject();

        /**
         * @var UserGradeRepository $repository
         */
        $repository = $manager->getRepository(UserGrade::class);

        if (!$entity instanceof UserGrade) {
            return;
        }

        $lastGrade = $repository->getLastUserGrade($entity->getUser());

        if (!$lastGrade) {
            return ;
        }

        /**
         * @var CycleRepository $repos
         */
        $repos = $manager->getRepository(Cycle::class);
        $cycle = $repos->getLastCycle();

        $lastGrade->setEndedAt($cycle->getEndedAt());
        $lastGrade->setStatus(false);
    }
}
