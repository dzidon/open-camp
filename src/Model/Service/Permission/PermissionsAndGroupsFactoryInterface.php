<?php

namespace App\Model\Service\Permission;

use App\Model\Library\Permission\PermissionsAndGroupsCreationResultInterface;

/**
 * Creates new permissions and permission groups.
 */
interface PermissionsAndGroupsFactoryInterface
{
    /**
     * Creates new permissions and permission groups.
     *
     * @return PermissionsAndGroupsCreationResultInterface
     */
    public function createPermissionsAndGroups(): PermissionsAndGroupsCreationResultInterface;
}