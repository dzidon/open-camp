<?php

namespace App\Model\Library\UserPasswordChange;

use App\Model\Entity\UserPasswordChange;

/**
 * Returned when a new user password change is created.
 */
interface UserPasswordChangeResultInterface
{
    /**
     * Returns the actual user password change entity.
     *
     * @return UserPasswordChange
     */
    public function getUserPasswordChange(): UserPasswordChange;

    /**
     * Returns the plain text verifier.
     *
     * @return string
     */
    public function getPlainVerifier(): string;

    /**
     * Returns the full token that can be used to finish the password change.
     *
     * @return string
     */
    public function getToken(): string;

    /**
     * A user password change is fake when it cannot be saved. For example, the specified email is not registered.
     *
     * @return bool
     */
    public function isFake(): bool;
}