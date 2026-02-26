<?php

namespace App\Services;

use App\Entity\Membership;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GetUpdatePrice
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(EntityManagerInterface $manager, TokenStorageInterface $tokenStorage)
    {
        $this->manager = $manager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param Membership|null $nextMembership
     * @param string|null $username
     * @return int|float|null
     */
    public function getLeftToPay(Membership $nextMembership = null, string $username = null)
    {
        if ($username) {
            $user_id = $this->getUserId($username);
            /**
             * @var User $user
             */
            $user = $this->manager->getRepository(User::class)->find((int)$user_id);
        } else {
            /**
             * @var User $user
             */
            $user = $this->tokenStorage->getToken()->getUser();
        }

        if (!$nextMembership) {
            return null;
        }

        return $nextMembership->getMembershipCost() - $user->getMembership()->getMembershipCost();
    }

    /**
     * @param string $username
     * @return false|string
     */
    private function getUserId(string $username)
    {
        $user_tab = explode(" ", $username);
        return substr(trim($user_tab[count($user_tab) - 1]), 1, -1);
    }
}
