<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\RoleSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\Role;
use Symfony\Component\Uid\UuidV4;

/**
 * Admin role CRUD.
 */
interface RoleRepositoryInterface
{
    /**
     * Saves a role.
     *
     * @param Role $role
     * @param bool $flush
     * @return void
     */
    public function saveRole(Role $role, bool $flush): void;

    /**
     * Removes a role.
     *
     * @param Role $role
     * @param bool $flush
     * @return void
     */
    public function removeRole(Role $role, bool $flush): void;

    /**
     * Creates a role.
     *
     * @param string $label
     * @return Role
     */
    public function createRole(string $label): Role;

    /**
     * Finds all roles.
     *
     * @return Role[]
     */
    public function findAll(): array;

    /**
     * Finds one role by id.
     *
     * @param UuidV4 $id
     * @return Role|null
     */
    public function findOneById(UuidV4 $id): ?Role;

    /**
     * Finds one role by label.
     *
     * @param string $label
     * @return Role|null
     */
    public function findOneByLabel(string $label): ?Role;

    /**
     * Returns admin role search paginator.
     *
     * @param RoleSearchData $data
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(RoleSearchData $data, int $currentPage, int $pageSize): PaginatorInterface;
}