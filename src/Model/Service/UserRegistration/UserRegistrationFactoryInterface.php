<?php

namespace App\Model\Service\UserRegistration;

use App\Model\Library\UserRegistration\UserRegistrationResultInterface;

/**
 * Handles creating user registrations.
 */
interface UserRegistrationFactoryInterface
{
    /**
     * This method creates a new UserRegistration in the database, and it ensures that its selector is unique.
     * Fake registrations must be supported. When a registration is fake, it is not persisted.
     * A registration is fake when:
     * - The given email is already registered.
     * - The amount of active registrations was reached.
     *
     * @param string $email
     * @return UserRegistrationResultInterface
     */
    public function createUserRegistration(string $email): UserRegistrationResultInterface;
}