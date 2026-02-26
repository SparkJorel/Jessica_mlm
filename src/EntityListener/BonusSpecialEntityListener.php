<?php

namespace App\EntityListener;

use App\Entity\BonusSpecial;
use App\Entity\Cycle;
use App\Repository\BonusSpecialRepository;
use App\Repository\CycleRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Exception;

class BonusSpecialEntityListener
{
    /**
     * @param BonusSpecial $bonusSpecial
     * @param LifecycleEventArgs $event
     */
    public function prePersist(BonusSpecial $bonusSpecial, LifecycleEventArgs $event)
    {
        $manager = $event->getEntityManager();

        $bonusSpecial->computeSlug();
        $bonusSpecial->setStatus(true);

        /**
         * @var CycleRepository $repository
         */
        $repository = $manager->getRepository(Cycle::class);


        $cycle = $repository->getLastCycle();
        $bonusSpecial->setStartedAt($cycle->getStartedAt());

        /**
         * @var BonusSpecialRepository $repositoryBS
         */
        $repositoryBS = $manager->getRepository(BonusSpecial::class);

        $prevBonusSpecial = $repositoryBS->getLastBonusSpecial($bonusSpecial);

        if (!$prevBonusSpecial) {
            return ;
        }

        $prevBonusSpecial->setStatus(false);
        $prevBonusSpecial->setEndedAt($cycle->getEndedAt());
    }

    /**
     * @param BonusSpecial $bonusSpecial
     * @param LifecycleEventArgs $event
     * @throws Exception
     */
    public function preUpdate(BonusSpecial $bonusSpecial, LifecycleEventArgs $event)
    {
        $bonusSpecial->computeSlug();
        $bonusSpecial->setUpdatedAt(
            new DateTime("now", new DateTimeZone("Africa/Douala"))
        );
    }
}
