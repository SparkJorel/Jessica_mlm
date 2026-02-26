<?php

namespace App\Security;

use App\Entity\User;
use App\Exception\AccountDeletedException;
use App\Exception\AccountNotActivatedException;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    /**
     * Checks the user account before authentication.
     * @param UserInterface $user
     *
     * @throws AccountStatusException
     */
    public function checkPreAuth(UserInterface $user)
    {
        // TODO: Implement checkPreAuth() method.
        if (!$user instanceof User) {
            return;
        }

        if (!$user->getActivated()) {
            throw new AccountNotActivatedException();
        }

        if ($user->isDeleted()) {
            throw new AccountDeletedException();
        }
    }

    /**
     * Checks the user account after authentication.
     * @param UserInterface $user
     *
     * @throws AccountStatusException
     */
    public function checkPostAuth(UserInterface $user)
    {
        // TODO: Implement checkPostAuth() method.
        if (!$user instanceof User) {
            return ;
        }

        if ($user->isExpired() || !$user->isActivated()) {
            throw new AccountExpiredException('User account expired or not activated !!!');
        }
    }
}
