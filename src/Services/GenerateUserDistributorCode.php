<?php

namespace App\Services;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;

class GenerateUserDistributorCode
{
    /** @var EntityManagerInterface */
    private $manager;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->manager = $entityManagerInterface;
    }

    /**
     * @param User $user
     * @return string
     */
    public function generateCode(User $user): string
    {
        $yearMonth = $user->getDateActivation()->format('ym');
        return $user->getCountry() . $yearMonth ."-". $this->getMonthlySubscription($yearMonth);
    }

    /**
     * @param string $yearMonth
     * @return string
     */
    private function getMonthlySubscription(string $yearMonth): string
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->manager->getRepository(User::class);

        $number = $userRepository->countMonthlySubscription($yearMonth);

        if (!$number || $number === 0) {
            return '0001';
        }

        $number += 1;

        if ($number > 0 && $number < 10) {
            return '000'.$number;
        }

        if ($number > 9 && $number < 100) {
            return '00'.$number;
        }

        if ($number > 99 && $number < 1000) {
            return '0'.$number;
        }

        if ($number > 999) {
            return (string)$number;
        }
    }
}
