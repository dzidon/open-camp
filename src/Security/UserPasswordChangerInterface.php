<?php

namespace App\Security;

use App\Entity\UserPasswordChange;

/**
 * Handles changing user passwords.
 */
interface UserPasswordChangerInterface
{
    /**
     * This method creates a new UserPasswordChange in the database, and it ensures that its selector is unique.
     *
     * @param string $email
     * @param bool $flush
     * @return UserPasswordChangeResultInterface
     */
    public function createUserPasswordChange(string $email, bool $flush): UserPasswordChangeResultInterface;

    /**
     * Hashes the plain text verifier and if it matches the user password change's hash, true is returned.
     *
     * @param UserPasswordChange $userPasswordChange
     * @param string $plainVerifier
     * @return bool
     */
    public function verify(UserPasswordChange $userPasswordChange, string $plainVerifier): bool;

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