<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * AccountDeletedException is thrown when the user account has been deleted.
 *
 * @author MKS
 */
class AccountDeletedException extends AccountStatusException
{
    /**
     * {@inheritdoc}
     */
    public function getMessageKey(): string
    {
        return 'Account not found or has been deleted.';
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageData(): array
    {
        return [];
    }
}
