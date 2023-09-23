<?php

namespace App\Model\Repository;

use App\Model\Entity\UserRegistration;

interface UserRegistrationRepositoryInterface
{
    /**
     * Saves a user registration.
     *
     * @param UserRegistration $userRegistration
     * @param bool $flush
     * @return void
     */
    public function saveUserRegistration(UserRegistration $userRegistration, bool $flush): void;

    /**
     * Removes a user registration.
     *
     * @param UserRegistration $userRegistration
     * @param bool $flush
     * @return void
     */
    public function removeUserRegistration(UserRegistration $userRegistration, bool $flush): void;

    /**
     * Finds one user registration by selector.
     *
     * @param string $selector
     * @param bool|null $active null = true & false
     * @return UserRegistration|null
     */
    public function findOneBySelector(string $selector, ?bool $active = null): ?UserRegistration;

    /**
     * Find all registrations assigned to an email address.
     *
     * @param string $email
     * @param bool|null $active null = true & false
     * @return UserRegistration[]
     */
    public function findByEmail(string $email, ?bool $active = null): array;
}