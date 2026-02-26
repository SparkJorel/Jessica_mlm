<?php

namespace App\Services;

use App\Entity\Cycle;
use App\Entity\ParameterConfig;
use App\Repository\CycleRepository;
use App\Repository\ParameterConfigRepository;
use DateInterval;
use DateTimeInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class ComputeDateOperation
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param DateTimeInterface $dateCommand
     * @return DateTimeInterface
     * @throws Exception
     */
    public function getDate(DateTimeInterface $dateCommand): DateTimeInterface
    {
        return $this->computeDateCommand($dateCommand);
    }

    /**
     * @param DateTimeInterface $dateCommand
     * @return DateTimeInterface
     * @throws Exception
     */
    private function computeDateCommand(DateTimeInterface $dateCommand): DateTimeInterface
    {
        /**
         * @var CycleRepository $repository
         */
        $repository = $this->manager->getRepository(Cycle::class);

        /** @var Cycle|null $cycle */
        $cycle = $repository->getLastCycle();
        if (!$cycle) {
            return $dateCommand;
        }

        /**
         * @var DateTime $startedAt 
         */
        $startedAt = $cycle->getStartedAt() ;

        /**
         * @var DateTime $endedAt 
         */
        $endedAt = $cycle->getEndedAt();

        if ($dateCommand >= $startedAt && $dateCommand <= $endedAt) {
            return $dateCommand;
        } else {
            $repo = $this->manager->getRepository(ParameterConfig::class);
            /**
             * @var ParameterConfigRepository $repo
             */
            $value = $repo->getCycleInterval();
            $interval = new DateInterval('PT'.(!$value ? 10 : $value).'M');
            return $endedAt->add($interval);
        }
    }

    /**
     * @return DateTimeInterface
     * @throws Exception
     */
    public function getNextStartCycle(): DateTimeInterface
    {
        return $this->addOrSubstractDateByInterval('add');
    }


    /**
     * @return DateTimeInterface
     * @throws Exception
     */
    public function getPrevEndCycle(): DateTimeInterface
    {
        return $this->addOrSubstractDateByInterval('sub');
    }

    /**
     * @param string $operation
     * @return DateTimeInterface
     * @throws Exception
     */
    private function addOrSubstractDateByInterval(string $operation = 'add'): DateTimeInterface
    {
        /**
         * @var CycleRepository $repositoryCycle
         */
        $repositoryCycle = $this->manager->getRepository(Cycle::class);

        /**
         * @var Cycle $cycle
         */
        $cycle = $repositoryCycle->getLastCycle();

        /** @var ParameterConfigRepository $repo */
        $repo = $this->manager->getRepository(ParameterConfig::class);

        $value = $repo->getCycleInterval();
        $interval = new DateInterval('PT'.(!$value ? 10 : $value).'M');

        if ($operation === 'add') {

            /** @var DateTime $dateOperation */
            $dateOperation = $cycle->getEndedAt();
            return $dateOperation->add($interval);
        } else {

            /** @var DateTime $dateOperation */
            $dateOperation = $cycle->getStartedAt();
            return $dateOperation->sub($interval);
        }
    }

    public function getManager()
    {
        return $this->manager;
    }
}
