<?php

namespace App\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AddMemberVoter extends Voter
{
    const ADD_MEMBER = 'add_member';

    protected function supports(string $attribute, mixed $subject): bool
    {
        $attributes = [self::ADD_MEMBER];

        if (!in_array($attribute, $attributes)) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return $user->getMembership()->getCoefficent() != 1;
    }
}
