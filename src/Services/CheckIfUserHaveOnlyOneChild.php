<?php

namespace App\Services;

use App\Entity\User;

class CheckIfUserHaveOnlyOneChild
{
    /**
     * @param User[] $users
     * @param User $user
     * @return bool
     */
    public function checkNumberofChildrenLessThanTwo(array $users, User $user)
    {
        $count = 0;

        foreach ($users as $u) {
            if ($u->getUpline()->getId() == $user->getId()) {
                $count++;
            }
        }

        if ($count > 1) {
            var_dump($count);
            die;
        }

        return true;
    }
}
