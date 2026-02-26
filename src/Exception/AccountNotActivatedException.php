<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * AccountNotActivatedException is thrown when the user account
 * hasn't yet been activated.
 *
 * @author MKS
 */
class AccountNotActivatedException extends AccountStatusException
{
    /**
     * {@inheritdoc}
     */
    public function getMessageKey(): string
    {
        return 'Account not activated. Please follow the link in the mail we previously sent you';
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageData(): string
    {
        return 'Account not activated. Please follow the link in the mail we previously sent you';
    }
}
