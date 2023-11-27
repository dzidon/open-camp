<?php

namespace App\Model\Service\UserPasswordChange;

use App\Model\Entity\UserPasswordChange;
use App\Model\Library\UserPasswordChange\UserPasswordChangeCompletionResultInterface;

/**
 * Handles changing user passwords.
 */
interface UserPasswordChangerInterface
{
    /**
     * If there is a user assigned to the password change, this method:
     * 1) Marks the user password change as used.
     * 2) Finds all other active password changes for the user and marks them as disabled.
     * 3) Changes the user password.
     *
     * If there is no user assigned, this method marks the user password change as disabled.
     *
     * @param UserPasswordChange $userPasswordChange
     * @param string $plainPassword
     * @return UserPasswordChangeCompletionResultInterface
     */
    public function completeUserPasswordChange(UserPasswordChange $userPasswordChange, string $plainPassword): UserPasswordChangeCompletionResultInterface;
}