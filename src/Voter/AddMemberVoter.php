<?php

namespace App\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AddMemberVoter extends Voter
{
    const ADD_MEMBER = 'add_member';

    protected function supports($attribute, $subject)
    {
        $attributes = [self::ADD_MEMBER];

        if (!in_array($attribute, $attributes)) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return $user->getMembership()->getCoefficent() != 1;
    }
}
