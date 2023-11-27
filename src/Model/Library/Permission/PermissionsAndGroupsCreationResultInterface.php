<?php

namespace App\Model\Library\Permission;

use App\Model\Entity\Permission;
use App\Model\Entity\PermissionGroup;

/**
 * Contains newly created permissions and permission groups.
 */
interface PermissionsAndGroupsCreationResultInterface
{
    /**
     * Returns newly created permissions.
     *
     * @return Permission[]
     */
    public function getCreatedPermissions(): array;

    /**
     * Returns newly created permission groups.
     *
     * @return PermissionGroup[]
     */
    public function getCreatedPermissionGroups(): array;
}