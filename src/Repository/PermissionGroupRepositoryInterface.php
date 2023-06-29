<?php

namespace App\Repository;

use App\Entity\PermissionGroup;

/**
 * Admin permission group CRUD.
 */
interface PermissionGroupRepositoryInterface
{
    /**
     * Saves a permission group.
     *
     * @param PermissionGroup $permissionGroup
     * @param bool $flush
     * @return void
     */
    public function savePermissionGroup(PermissionGroup $permissionGroup, bool $flush): void;

    /**
     * Removes a permission group.
     *
     * @param PermissionGroup $permissionGroup
     * @param bool $flush
     * @return void
     */
    public function removePermissionGroup(PermissionGroup $permissionGroup, bool $flush): void;

    /**
     * Creates a permission group.
     *
     * @param string $name
     * @param string $label
     * @param int $priority
     * @return PermissionGroup
     */
    public function createPermissionGroup(string $name, string $label, int $priority): PermissionGroup;

    /**
     * Finds all available permission groups.
     *
     * @return PermissionGroup[]
     */
    public function findAll(): array;
}