<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\UserSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\CampDate;
use App\Model\Entity\Role;
use App\Model\Entity\User;
use Symfony\Component\Uid\UuidV4;

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
     * Finds a user by their url name.
     *
     * @param string $urlName
     * @return User|null
     */
    public function findOneByUrlName(string $urlName): ?User;

    /**
     * Finds all users whose url names are not null.
     *
     * @return User[]
     */
    public function findThoseWithNotNullUrlNames(): array;

    /**
     * Finds all users that have the given role.
     *
     * @param null|Role $role
     * @return User[]
     */
    public function findByRole(?Role $role): array;

    /**
     * Finds all users that are assigned as guides to the given camp dates.
     *
     * @param CampDate[] $campDates
     * @return User[]
     */
    public function findByCampDates(array $campDates): array;

    /**
     * Returns admin user search paginator.
     *
     * @param UserSearchData $data
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(UserSearchData $data, int $currentPage, int $pageSize): PaginatorInterface;
}