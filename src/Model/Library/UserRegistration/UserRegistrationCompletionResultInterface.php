<?php

namespace App\Model\Library\UserRegistration;

use App\Model\Entity\User;
use App\Model\Entity\UserRegistration;

/**
 * Returned when a user registration is completed.
 */
interface UserRegistrationCompletionResultInterface
{
    /**
     * Returns the registered user.
     *
     * @return null|User
     */
    public function getUser(): ?User;

    /**
     * Returns the user registration that was marked as used.
     *
     * @return null|UserRegistration
     */
    public function getUsedUserRegistration(): ?UserRegistration;

    /**
     * Returns those user registrations that were marked as disabled.
     *
     * @return UserRegistration[]
     */
    public function getDisabledUserRegistrations(): array;
}