<?php

namespace App\Model\Module\Security\UserPasswordChange;

/**
 * Handles creating user password changes.
 */
interface UserPasswordChangeFactoryInterface
{
    /**
     * This method creates a new UserPasswordChange in the database, and it ensures that its selector is unique.
     * Fake password changes must be supported. When a password change is fake, it is not persisted.
     * A password change is fake when:
     * - The given email is not registered.
     * - The amount of active password changes was reached.
     *
     * @param string $email
     * @param bool $flush
     * @return UserPasswordChangeResultInterface
     */
    public function createUserPasswordChange(string $email, bool $flush): UserPasswordChangeResultInterface;
}