<?php

namespace App\Security\PasswordChange;

use App\Entity\UserPasswordChange;

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
     * @param bool $flush
     * @return void
     */
    public function completeUserPasswordChange(UserPasswordChange $userPasswordChange, string $plainPassword, bool $flush): void;
}