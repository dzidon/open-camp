<?php

namespace App\Repository;

use App\Entity\User;

/**
 * User CRUD.
 */
interface UserRepositoryInterface
{
    /**
     * Saves a user.
     *
     * @param User $user
     * @param bool $flush
     * @return void
     */
    public function saveUser(User $user, bool $flush): void;

    /**
     * Removes a user.
     *
     * @param User $user
     * @param bool $flush
     * @return void
     */
    public function removeUser(User $user, bool $flush): void;

    /**
     * Creates a user.
     *
     * @param string $email
     * @param string|null $plainPassword Gets hashed if not null.
     * @return User
     */
    public function createUser(string $email, ?string $plainPassword = null): User;

    /**
     * Finds one user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findOneByEmail(string $email): ?User;

    /**
     * Returns true if there is a user with the specified email.
     *
     * @param string $email
     * @return bool
     */
    public function isEmailRegistered(string $email): bool;
}