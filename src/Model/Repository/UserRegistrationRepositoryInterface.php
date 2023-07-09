<?php

namespace App\Model\Repository;

use App\Model\Entity\UserRegistration;
use DateTimeImmutable;

/**
 * User registration CRUD.
 */
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
     * Creates a user registration.
     *
     * @param string $email
     * @param DateTimeImmutable $expireAt
     * @param string $selector
     * @param string $plainVerifier
     * @return UserRegistration
     */
    public function createUserRegistration(string $email,
                                           DateTimeImmutable $expireAt,
                                           string $selector,
                                           string $plainVerifier): UserRegistration;

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