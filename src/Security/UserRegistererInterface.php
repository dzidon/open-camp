<?php

namespace App\Security;

use App\Entity\UserRegistration;

/**
 * Handles registering new users.
 */
interface UserRegistererInterface
{
    /**
     * This method creates a new UserRegistration in the database, and it ensures that its selector is unique.
     *
     * @param string $email
     * @param bool $flush
     * @return UserRegistrationResultInterface
     */
    public function createUserRegistration(string $email, bool $flush): UserRegistrationResultInterface;

    /**
     * Hashes the plain text verifier and if it matches the user registration's hash, true is returned.
     *
     * @param UserRegistration $userRegistration
     * @param string $plainVerifier
     * @return bool
     */
    public function verify(UserRegistration $userRegistration, string $plainVerifier): bool;

    /**
     * If there is no user with the specified email, this method:
     * 1) Marks the user registration as used.
     * 2) Finds all other active user registrations for the specified user registration by email and
     *    marks them as disabled.
     * 3) Creates a new user with the specified plain text password.
     *
     * If there is a user with the specified email, this method:
     * 1) Marks the user registration as disabled.
     * 2) Finds all other active user registrations for the specified user registration by email and
     *    marks them as disabled.
     *
     * @param UserRegistration $userRegistration
     * @param string $plainPassword
     * @param bool $flush
     * @return void
     */
    public function completeUserRegistration(UserRegistration $userRegistration, string $plainPassword, bool $flush): void;
}