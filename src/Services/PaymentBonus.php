<?php

namespace App\Services;

use App\Entity\User;
use App\Entity\UserPaidBonus;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;

class PaymentBonus
{
    /**
     * @var array
     */
    private $users;

    /**
     * @var string
     */
    private $reason;

    /**
     * @var string
     */
    private $month;

    /**
     * @var string
     */
    private $year;

    /**
     * @var DateTimeInterface
     */
    private $startedAt;

    /**
     * @var DateTimeInterface
     */
    private $endedAt;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function savePayment()
    {
        foreach ($this->users as $user_id) {
            /**
             * @var User $user
             */
            $user = $this->manager->getRepository(User::class)->find((int)$user_id);
            $userPaidBonus = (new UserPaidBonus())
                                        ->setUser($user)
                                        ->setMonth($this->month)
                                        ->setYear($this->year)
                                        ->setStartedAt($this->startedAt)
                                        ->setEndedAt($this->endedAt)
                                        ->setPaid(true)
                                        ->setReason($this->reason)
            ;

            $this->manager->persist($userPaidBonus);
        }

        $this->manager->flush();
    }

    public function setReason(string $reason)
    {
        $this->reason = $reason;
        return $this;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function setYear(string $year)
    {
        $this->year = $year;
        return $this;
    }

    public function getYear(): string
    {
        return $this->year;
    }

    public function setMonth(string $month)
    {
        $this->month = $month;
        return $this;
    }

    public function getMonth(): string
    {
        return $this->month;
    }

    public function setStartedAt(string $startedAt)
    {
        $this->startedAt = new DateTime($startedAt, new DateTimeZone('Africa/Douala'));
        return $this;
    }

    public function getStartedAt(): \DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setEndedAt(string $endedAt)
    {
        $this->endedAt = new DateTime($endedAt, new DateTimeZone('Africa/Douala'));
        return $this;
    }

    public function getEndedAt(): \DateTimeInterface
    {
        return $this->endedAt;
    }

    public function setUsers(array $users_id)
    {
        $this->users = $users_id;
        return $this;
    }

    public function getUsers()
    {
        return $this->users;
    }
}
