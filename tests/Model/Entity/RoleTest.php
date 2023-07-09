<?php

namespace App\Tests\Model\Entity;

use App\Model\Entity\Permission;
use App\Model\Entity\PermissionGroup;
use App\Model\Entity\Role;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    private const LABEL = 'Label...';

    private Role $role;

    public function testLabel(): void
    {
        $this->assertSame(self::LABEL, $this->role->getLabel());

        $newLabel = 'New label';
        $this->role->setLabel($newLabel);
        $this->assertSame($newLabel, $this->role->getLabel());
    }

    public function testGetPermissions(): void
    {
        $permissions = $this->role->getPermissions();

        $permissionNames = [];
        foreach ($permissions as $permission)
        {
            $permissionNames[] = $permission->getName();
        }

        $this->assertSame(['perm1', 'perm2', 'perm3', 'perm4'], $permissionNames);
    }

    public function testGetPermissionsGroupedByName(): void
    {
        $permissionsGrouped = $this->role->getPermissionsGrouped();

        $expectedArray = [
            'group1' => ['perm1', 'perm2'],
            'group2' => ['perm3', 'perm4'],
        ];

        $permissionsGroupedNames = [];
        foreach ($permissionsGrouped as $groupName => $permissions)
        {
            /** @var Permission $permission */
            foreach ($permissions as $permission)
            {
                $permissionsGroupedNames[$groupName][] = $permission->getName();
            }
        }

        $this->assertSame($expectedArray, $permissionsGroupedNames);
    }

    public function testGetPermissionsGroupedByLabel(): void
    {
        $permissionsGrouped = $this->role->getPermissionsGrouped(true);

        $expectedArray = [
            'Group 1' => ['perm1', 'perm2'],
            'Group 2' => ['perm3', 'perm4'],
        ];

        $permissionsGroupedNames = [];
        foreach ($permissionsGrouped as $groupLabel => $permissions)
        {
            /** @var Permission $permission */
            foreach ($permissions as $permission)
            {
                $permissionsGroupedNames[$groupLabel][] = $permission->getName();
            }
        }

        $this->assertSame($expectedArray, $permissionsGroupedNames);
    }

    public function testAddAndRemovePermission(): void
    {
        $group = new PermissionGroup('group_new', 'Group new', 0);
        $perm = new Permission('perm_new', 'Permission new', 0, $group);

        $this->role->addPermission($perm);
        $this->assertContains($perm, $this->role->getPermissions());

        $this->role->removePermission($perm);
        $this->assertNotContains($perm, $this->role->getPermissions());
    }

    protected function setUp(): void
    {
        $this->role = new Role(self::LABEL);

        $group1 = new PermissionGroup('group1', 'Group 1', 0);
        $perm1 = new Permission('perm1', 'Permission 1', 0, $group1);
        $perm2 = new Permission('perm2', 'Permission 2', 0, $group1);

        $group2 = new PermissionGroup('group2', 'Group 2', 1);
        $perm3 = new Permission('perm3', 'Permission 3', 1, $group2);
        $perm4 = new Permission('perm4', 'Permission 4', 1, $group2);

        $this->role
            ->addPermission($perm1)
            ->addPermission($perm2)
            ->addPermission($perm3)
            ->addPermission($perm4)
        ;
    }
}