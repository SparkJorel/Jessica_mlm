<?php

namespace App\EventListener;

use App\Entity\BonusSpecial;
use App\Entity\Cycle;
use App\Entity\UserBonusSpecial;
use App\Event\UserGradeReachedEvent;
use App\Repository\CycleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserGradeReachedSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserGradeReachedEvent::class => 'addNewEntryUserBonusSpecial'
        ];
    }

    public function addNewEntryUserBonusSpecial(UserGradeReachedEvent $event): void
    {
        $user = $event->getUser();
        $grade = $event->getGrade();

        /**
         * @var BonusSpecial $bonusSpecial
         */
        $bonusSpecial = $this
                            ->manager
                            ->getRepository(BonusSpecial::class)
                            ->findOneBy([
                                'grade' => $grade,
                                'status' => true
                            ])
        ;

        if (!$bonusSpecial) {
            return ;
        }

        /**
         * @var CycleRepository $repository
         */
        $repository = $this->manager->getRepository(Cycle::class);

        $startedAt = $repository->getLastCycle()->getStartedAt();

        $userBonusSpecial = (new UserBonusSpecial())
                            ->setUser($user)
                            ->setBonus($bonusSpecial)
                            ->setStartedAt($startedAt)
                            ->setStatus(false)
                            ->setPromo(false)
                            ->setFirstCondition(true)
                            ->setSecondCondition(false)
            ;

        $this->manager->persist($userBonusSpecial);
    }
}
