<?php

namespace App\Repository;

use App\Entity\Role;
use App\Form\DataTransfer\Data\Admin\RoleSearchDataInterface;
use App\Search\Paginator\PaginatorInterface;

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
     * Finds one role by id.
     *
     * @param int $id
     * @return Role|null
     */
    public function findOneById(int $id): ?Role;

    /**
     * Returns admin role search paginator.
     *
     * @param RoleSearchDataInterface $data
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(RoleSearchDataInterface $data, int $currentPage, int $pageSize): PaginatorInterface;
}