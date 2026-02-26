<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class GetUplineKnowingSponsor
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
     * @param User $user
     * @return User|null
     */
    public function getUplineKnwoingIDSponsor(User $user)
    {
        /**
         * @var UserRepository $repository 
         */
        $repository = $this->manager->getRepository(User::class);

        $upline_id = $repository
                        ->findUplineKnowingSponsor(
                            $user->getSponsor()->getId(),
                            $user->getPosition()
                        );

        if (is_array($upline_id)) {
            return $repository->find($upline_id['u_id']);
        } else {
            return null;
        }
    }
}
