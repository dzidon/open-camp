<?php

namespace App\Model\Repository;

use App\Model\Entity\PermissionGroup;

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
     * Finds all available permission groups.
     *
     * @return PermissionGroup[]
     */
    public function findAll(): array;
}