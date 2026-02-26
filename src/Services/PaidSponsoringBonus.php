<?php

namespace App\Services;

use App\Entity\Cycle;
use App\Entity\SponsoringBonus;
use App\Entity\User;
use App\Repository\SponsoringBonusRepository;
use Doctrine\ORM\EntityManagerInterface;

class PaidSponsoringBonus
{
    /**
     * @var array
     */
    private $users_id;

    /**
     * @var int
     */
    private $cycle;

    public function __construct(array $users_id, int $cycle)
    {
        $this->users_id = $users_id;
        $this->cycle = $cycle;
    }

    public function paid(EntityManagerInterface $manager)
    {
        /**
         * @var Cycle $cycle
         */
        $cycle = $manager->getRepository(Cycle::class)->find($this->cycle);

        /**
         * @var SponsoringBonusRepository $repository
         */
        $repository = $manager->getRepository(SponsoringBonus::class);

        foreach ($this->users_id as $user_id) {
            /**
             * @var User $user
             */
            $user = $manager->getRepository(User::class)->find((int)$user_id);

            /**
             * @var SponsoringBonus[]
             */
            $bonuses = $repository->getBonusSponsoringByUserByCycle($user, $cycle, false);

            foreach ($bonuses as $bonus) {
                $bonus->setPaid(true);
            }
        }

        $manager->flush();
    }
}
