<?php

namespace App\Model\Module\Security\UserRegistration;

use App\Model\Entity\UserRegistration;

/**
 * Handles completing registrations and creating new users.
 */
interface UserRegistererInterface
{
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