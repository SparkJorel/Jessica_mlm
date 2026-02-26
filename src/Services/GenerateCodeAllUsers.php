<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Repository\UserRepository;

class GenerateCodeAllUsers
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function generateUsersCode(): string
    {
        $users = $this->getAllActivatedUsers();

        if (!$users) {
            return "Les distributeurs disposent déjà d'un code";
        }

        $this->processUserCode($users);

        $this->manager->flush();

        return "mise à jour des codes effectuée avec succès";
    }

    /**
     * @return array|null
     */
    private function getAllActivatedUsers(): ?array
    {
        $usersOrdered = [];

        /** @var UserRepository $userRepository */
        $userRepository = $this->manager->getRepository(User::class);

        /** @var User[]|null $users */
        $users = $userRepository->getAllActivatedUsers();

        if (!$users) {
            return null;
        }

        foreach ($users as $user) {
            $usersOrdered[$user->getDateActivation()->format('ym')][] = $user;
        }

        return $usersOrdered;
    }

    /**
     * @param array $usersOrdered
     * @return void
     */
    private function processUserCode(array $usersOrdered): void
    {
        /** @var User[] $users */
        foreach ($usersOrdered as $key => $users) {
            foreach ($users as $index => $user) {
                $code = $user->getCountry() . $key;

                if (($index+1) >= 1 && ($index+1) < 10) {
                    $code .= "-" . '000' . ($index + 1);
                } elseif (($index+1) > 9 && ($index+1) < 100) {
                    $code .= "-" . '00' . ($index + 1);
                } elseif (($index+1) > 99 && ($index+1) < 1000) {
                    $code .= "-" . '0' .  ($index + 1);
                } else {
                    $code .= "-" . ($index + 1);
                }

                $user->setCodeDistributor($code);

                $code = "";
            }
        }
    }
}
