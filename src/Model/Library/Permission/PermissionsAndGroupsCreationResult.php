<?php

namespace App\Model\Library\Permission;

use App\Model\Entity\Permission;
use App\Model\Entity\PermissionGroup;
use LogicException;

/**
 * @inheritDoc
 */
class PermissionsAndGroupsCreationResult implements PermissionsAndGroupsCreationResultInterface
{
    /**
     * @var Permission[]
     */
    private array $permissions;

    /**
     * @var PermissionGroup[]
     */
    private array $permissionGroups;

    public function __construct(array $permissions = [], array $permissionGroups = [])
    {
        foreach ($permissions as $permission)
        {
            if (!$permission instanceof Permission)
            {
                throw new LogicException(
                    sprintf("Permissions passed to the constructor of %s must all be instances of %s.", self::class, Permission::class)
                );
            }
        }

        foreach ($permissionGroups as $permissionGroup)
        {
            if (!$permissionGroup instanceof PermissionGroup)
            {
                throw new LogicException(
                    sprintf("Permission groups passed to the constructor of %s must all be instances of %s.", self::class, PermissionGroup::class)
                );
            }
        }

        $this->permissions = $permissions;
        $this->permissionGroups = $permissionGroups;
    }

    /**
     * @inheritDoc
     */
    public function getCreatedPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * @inheritDoc
     */
    public function getCreatedPermissionGroups(): array
    {
        return $this->permissionGroups;
    }
}