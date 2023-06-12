<?php

namespace App\Security;

use App\Entity\UserRegistration;

/**
 * Returned when a new user registration is created.
 */
interface UserRegistrationCreationResultInterface
{
    /**
     * Returns the actual user registration entity.
     *
     * @return UserRegistration
     */
    public function getUserRegistration(): UserRegistration;

    /**
     * A user registration is fake when it cannot be saved. For example, the specified email is already registered.
     *
     * @return bool
     */
    public function isFake(): bool;

    /**
     * Gets the token verifier in plain text.
     *
     * @return string
     */
    public function getPlainVerifier(): string;
}