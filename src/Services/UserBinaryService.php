<?php

namespace App\Services;

use App\Entity\User;
use App\Entity\UserBinaryCycle;
use App\Repository\UserBinaryCycleRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Cette classe sauvegarde le binaire d'un utilisateur au cours d'un cycle
 * Affiche la liste des binaires enregistrés
 * Affiche la liste des binaires d'un utilisateur
 *
 * Class UserBinaryService
 * @package App\Services
 */
class UserBinaryService
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
     * @param UserBinaryCycle $userBinaryCycle
     * @return bool
     */
    public function saveUserBinary(UserBinaryCycle $userBinaryCycle)
    {
        $found = $this
                    ->manager
                    ->getRepository(UserBinaryCycle::class)
                    ->findOneBy([
                        'user' => $userBinaryCycle->getUser(),
                        'cycle' => $userBinaryCycle->getCycle(),
                    ]);

        if (!$found) {
            $this->manager->persist($userBinaryCycle);
            $this->manager->flush();

            return true;
        }

        return false;
    }

    public function listAllUsersBinaries()
    {
        return $this
                    ->manager
                    ->getRepository(UserBinaryCycle::class)
                    ->findAll();
    }

    public function findUserBinariesByUser(User $user)
    {
        /**
         * @var UserBinaryCycleRepository $repository
         */
        $repository = $this->manager->getRepository(UserBinaryCycle::class);

        return $repository->findByUser($user);
    }
}
