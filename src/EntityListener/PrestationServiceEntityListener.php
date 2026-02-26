<?php

namespace App\EntityListener;

use App\Entity\Cycle;
use App\Entity\PrestationService;
use App\Repository\CycleRepository;
use App\Repository\PrestationServiceRepository;
use App\Services\ComputeDateOperation;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Exception;

class PrestationServiceEntityListener
{
    /**
     * @var ComputeDateOperation
     */
    private $computeDateOperation;

    public function __construct(ComputeDateOperation $computeDateOperation)
    {
        $this->computeDateOperation = $computeDateOperation;
    }

    /**
     * @param PrestationService $prestationService
     * @param LifecycleEventArgs $event
     * @throws Exception
     */
    public function prePersist(PrestationService $prestationService, LifecycleEventArgs $event)
    {
        $prestationService->computeSlug();
        $prestationService
            ->setRecordedAt(
                new DateTime(
                    "now",
                    new DateTimeZone("Africa/Douala")
                )
            );

        if ($prestationService->getStatus()) {
            $manager = $event->getEntityManager();

            /**
             * @var CycleRepository $repositoryCycle
             */
            $repositoryCycle = $manager->getRepository(Cycle::class);

            $startedAt = $repositoryCycle->getLastCycle()->getStartedAt();
            $prestationService->setStartedAt($startedAt);

            /**
             * @var PrestationServiceRepository $repository
             */
            $repository = $manager->getRepository(PrestationService::class);

            /**
             * @var PrestationService|null $prevPrestationService
             */
            $prevPrestationService = $repository
                ->getLastPrestationService(
                    $prestationService->getCode()
                );

            if (!$prevPrestationService) {
                return ;
            }

            $prevPrestationService
                ->setStatus(false)
                ->setEndedAt($this->computeDateOperation->getPrevEndCycle());
        }
    }

    /**
     * @param PrestationService $prestationService
     * @param LifecycleEventArgs $event
     * @throws Exception
     */
    public function preUpdate(PrestationService $prestationService, LifecycleEventArgs $event)
    {
        $prestationService->computeSlug();

        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        $originalPrestationService = $uow->getOriginalEntityData($prestationService);

        if (is_array($originalPrestationService) &&
            array_key_exists('status', $originalPrestationService)) {
            if (false === $originalPrestationService['status'] &&
                $prestationService->getStatus()) {

                /**
                 * @var CycleRepository $repositoryCycle
                 */
                $repositoryCycle = $em->getRepository(Cycle::class);

                $startedAt = $repositoryCycle->getLastCycle()->getStartedAt();
                $prestationService->setStartedAt($startedAt);

                $manager = $event->getEntityManager();

                /**
                 * @var PrestationServiceRepository $repository
                 */
                $repository = $manager
                    ->getRepository(PrestationService::class);

                /**
                 * @var PrestationService|null $prevPrestationService
                 */
                $prevPrestationService = $repository
                    ->getLastPrestationService(
                        $prestationService->getCode()
                    );

                if (!$prevPrestationService) {
                    return ;
                }

                $prevPrestationService
                    ->setStatus(false)
                    ->setEndedAt($this->computeDateOperation->getPrevEndCycle());
            }
        }
    }
}
