<?php

namespace App\Model\Service\UserRegistration;

use App\Model\Entity\UserRegistration;
use App\Model\Library\UserRegistration\UserRegistrationCompletionResultInterface;

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
     * @return UserRegistrationCompletionResultInterface
     */
    public function completeUserRegistration(UserRegistration $userRegistration, string $plainPassword): UserRegistrationCompletionResultInterface;
}