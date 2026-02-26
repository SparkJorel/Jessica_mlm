<?php

namespace App\EventListener;

use Exception;
use App\Entity\User;
use App\Entity\Grade;
use App\Entity\UserGrade;
use App\Event\ChangeGradeEvent;
use App\Repository\GradeRepository;
use App\Event\UserGradeReachedEvent;
use App\Services\ComputeDateOperation;
use App\Repository\UserGradeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ChangeGradeSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var ComputeDateOperation
     */
    private $compute;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(
        EntityManagerInterface $manager,
        ComputeDateOperation  $compute,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->manager = $manager;
        $this->compute = $compute;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ChangeGradeEvent::class => 'changeGradeUser'
        ];
    }

    /**
     * @param ChangeGradeEvent $event
     * @throws Exception
     */
    public function changeGradeUser(ChangeGradeEvent $event): void
    {
        $user = $event->getUser();
        $binaire = $event->getBinaire();

        /**
         * @var GradeRepository $repository
         */
        $repository = $this->manager->getRepository(Grade::class);

        $grade = $repository->getGradeMatchingSV($binaire);

        if (!$grade) {
            return;
        }

        $isUserAlreadyHaveGrade = $this->isUserHasGrade($user, $grade);

        if ($isUserAlreadyHaveGrade) {
            return;
        }

        $userGrade = (new UserGrade())
                                ->setUser($user)
                                ->setGrade($grade)
                                ->setStatus(true)
                                ->setStartedAt($this->compute->getNextStartCycle());

        $user->setGrade($grade->getCommercialName());
        $user->setUserGrade($grade);

        $this->manager->persist($userGrade);

        if ($grade->isRewardable()) {
            $this->dispatcher->dispatch(
                new UserGradeReachedEvent($user, $grade)
            );
        }
    }

    /**
     * @param User $user
     * @param Grade $grade
     * @return bool
     */
    private function isUserHasGrade(User $user, Grade $grade)
    {
        /**
         * @var UserGradeRepository $repository
         */
        $repository = $this->manager->getRepository(UserGrade::class);
        $userGrade  = $repository->checkUserGrade($user, $grade);

        if (!$userGrade) {
            return false;
        }

        return true;
    }
}
