<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserPasswordChange;
use DateTimeImmutable;

/**
 * User password change CRUD.
 */
interface UserPasswordChangeRepositoryInterface
{
    /**
     * Saves a user password change.
     *
     * @param UserPasswordChange $userPasswordChange
     * @param bool $flush
     * @return void
     */
    public function saveUserPasswordChange(UserPasswordChange $userPasswordChange, bool $flush): void;

    /**
     * Removes a user password change.
     *
     * @param UserPasswordChange $userPasswordChange
     * @param bool $flush
     * @return void
     */
    public function removeUserPasswordChange(UserPasswordChange $userPasswordChange, bool $flush): void;

    /**
     * Creates a user password change.
     *
     * @param DateTimeImmutable $expireAt
     * @param string $selector
     * @param string $plainVerifier
     * @return UserPasswordChange
     */
    public function createUserPasswordChange(DateTimeImmutable $expireAt, string $selector, string $plainVerifier): UserPasswordChange;

    /**
     * Finds one user password change by selector.
     *
     * @param string $selector
     * @param bool|null $active null = true & false
     * @return UserPasswordChange|null
     */
    public function findOneBySelector(string $selector, ?bool $active = null): ?UserPasswordChange;

    /**
     * Find all user password changes assigned to a user.
     *
     * @param User $user
     * @param bool|null $active null = true & false
     * @return UserPasswordChange[]
     */
    public function findByUser(User $user, ?bool $active = null): array;
}