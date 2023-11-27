<?php

namespace App\Model\Service\UserPasswordChange;

use App\Model\Library\UserPasswordChange\UserPasswordChangeResultInterface;

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
     * @return UserPasswordChangeResultInterface
     */
    public function createUserPasswordChange(string $email): UserPasswordChangeResultInterface;
}