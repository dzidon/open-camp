<?php

namespace App\Model\Repository;

use App\Model\Entity\User;
use App\Model\Entity\UserPasswordChange;

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