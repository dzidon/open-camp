<?php

namespace App\Model\Repository;

use App\Form\DataTransfer\Data\Admin\RoleSearchDataInterface;
use App\Model\Entity\Role;
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
     * Finds all roles.
     *
     * @return Role[]
     */
    public function findAll(): array;

    /**
     * Finds one role by id.
     *
     * @param int $id
     * @return Role|null
     */
    public function findOneById(int $id): ?Role;

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
     * @param RoleSearchDataInterface $data
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(RoleSearchDataInterface $data, int $currentPage, int $pageSize): PaginatorInterface;
}