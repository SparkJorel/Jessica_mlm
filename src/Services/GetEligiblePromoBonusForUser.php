<?php

namespace App\Services;

use App\Entity\PromoBonusSpecial;
use App\Entity\User;
use App\Entity\UserBonusSpecial;
use App\Repository\PromoBonusSpecialRepository;
use App\Repository\UserBonusSpecialRepository;
use Doctrine\ORM\EntityManagerInterface;

class GetEligiblePromoBonusForUser
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    protected function getAvailablePromoBonus(string $gradeName = null)
    {
        /**
         * @var PromoBonusSpecialRepository $repository
         */
        $repository = $this->manager->getRepository(PromoBonusSpecial::class);

        if (!$gradeName) {
            $promos = $repository->findBy([
                'status' => true,
                'underCondition' => false
            ]);
        } else {
            //$promos
        }
    }

    protected function retrievesPromoReached(User $user)
    {
        /**
         * @var UserBonusSpecialRepository $repository
         */
        $repository = $this->manager->getRepository(UserBonusSpecial::class);

        $userBonus = $repository->findBy([
           'user' => $user
        ]);
    }
}
