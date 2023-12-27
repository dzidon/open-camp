<?php

namespace App\Model\Repository;

use App\Model\Entity\User;
use App\Model\Entity\UserPasswordChange;

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
     * Finds one user password change by selector.
     *
     * @param string $selector
     * @param bool|null $active null = true & false
     * @return UserPasswordChange|null
     */
    public function findOneBySelector(string $selector, ?bool $active = null): ?UserPasswordChange;

    /**
     * Returns true if the selector already exists.
     *
     * @param string $selector
     * @param bool|null $active
     * @return bool
     */
    public function selectorExists(string $selector, ?bool $active = null): bool;

    /**
     * Find all user password changes assigned to a user.
     *
     * @param User $user
     * @param bool|null $active null = true & false
     * @return UserPasswordChange[]
     */
    public function findByUser(User $user, ?bool $active = null): array;
}