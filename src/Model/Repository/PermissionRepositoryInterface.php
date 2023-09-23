<?php

namespace App\Model\Repository;

use App\Model\Entity\Permission;

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
     * Finds all available permissions.
     *
     * @return Permission[]
     */
    public function findAll(): array;
}