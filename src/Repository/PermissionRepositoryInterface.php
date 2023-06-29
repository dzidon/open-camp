<?php

namespace App\Repository;

use App\Entity\Permission;
use App\Entity\PermissionGroup;

/**
 * Admin permission CRUD.
 */
interface PermissionRepositoryInterface
{
    /**
     * Saves a permission.
     *
     * @param Permission $permission
     * @param bool $flush
     * @return void
     */
    public function savePermission(Permission $permission, bool $flush): void;

    /**
     * Removes a permission.
     *
     * @param Permission $permission
     * @param bool $flush
     * @return void
     */
    public function removePermission(Permission $permission, bool $flush): void;

    /**
     * Creates a permission.
     *
     * @param string $name
     * @param string $label
     * @param int $priority
     * @param PermissionGroup $group
     * @return Permission
     */
    public function createPermission(string $name, string $label, int $priority, PermissionGroup $group): Permission;

    /**
     * Finds all available permissions.
     *
     * @return Permission[]
     */
    public function findAll(): array;
}