<?php

namespace App\Security;

use App\Entity\UserRegistration;

/**
 * A facade that handles registering new users.
 */
interface UserRegistererInterface
{
    /**
     * This method:
     * 1) Creates a new UserRegistration in the database.
     * 2) Sends a registration completion url to the user.
     *
     * @param string $email
     * @return void
     */
    public function createUserRegistration(string $email): void;

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
     * @return void
     */
    public function completeUserRegistration(UserRegistration $userRegistration, string $plainPassword): void;
}