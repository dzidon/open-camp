<?php

namespace App\Model\Library\UserPasswordChange;

use App\Model\Entity\UserPasswordChange;

/**
 * Returned when a user password change is completed.
 */
interface UserPasswordChangeCompletionResultInterface
{
    /**
     * Returns the user password change that was marked as used.
     *
     * @return null|UserPasswordChange
     */
    public function getUsedUserPasswordChange(): ?UserPasswordChange;

    /**
     * Returns those user password changes that were marked as disabled.
     *
     * @return UserPasswordChange[]
     */
    public function getDisabledUserPasswordChanges(): array;
}