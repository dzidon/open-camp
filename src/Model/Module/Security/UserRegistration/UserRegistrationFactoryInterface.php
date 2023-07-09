<?php

namespace App\Model\Module\Security\UserRegistration;

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
     * @param bool $flush
     * @return UserRegistrationResultInterface
     */
    public function createUserRegistration(string $email, bool $flush): UserRegistrationResultInterface;
}