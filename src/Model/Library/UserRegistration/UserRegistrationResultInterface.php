<?php

namespace App\Model\Library\UserRegistration;

use App\Model\Entity\UserRegistration;

/**
 * Returned when a new user registration is created.
 */
interface UserRegistrationResultInterface
{
    /**
     * Returns the actual user registration entity.
     *
     * @return UserRegistration
     */
    public function getUserRegistration(): UserRegistration;

    /**
     * Returns the plain text verifier.
     *
     * @return string
     */
    public function getPlainVerifier(): string;

    /**
     * Returns the full token that can be used to finish the registration.
     *
     * @return string
     */
    public function getToken(): string;

    /**
     * A user registration is fake when it cannot be saved. For example, the specified email is already registered.
     *
     * @return bool
     */
    public function isFake(): bool;
}