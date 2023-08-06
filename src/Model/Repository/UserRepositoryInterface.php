<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\UserSearchDataInterface;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\Role;
use App\Model\Entity\User;
use Symfony\Component\Uid\UuidV4;

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
     * Finds one user by id.
     *
     * @param UuidV4 $id
     * @return User|null
     */
    public function findOneById(UuidV4 $id): ?User;

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

    /**
     * Finds all users that have the given role.
     *
     * @param null|Role $role
     * @return User[]
     */
    public function findByRole(?Role $role): array;

    /**
     * Returns admin user search paginator.
     *
     * @param UserSearchDataInterface $data
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(UserSearchDataInterface $data, int $currentPage, int $pageSize): PaginatorInterface;
}