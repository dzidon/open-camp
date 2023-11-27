<?php

namespace App\Tests\Model\Library\Permission;

use App\Model\Entity\Permission;
use App\Model\Entity\PermissionGroup;
use App\Model\Library\Permission\PermissionsAndGroupsCreationResult;
use LogicException;
use PHPUnit\Framework\TestCase;
use stdClass;

class PermissionsAndGroupsCreationResultTest extends TestCase
{
    private array $permissions;

    private array $permissionGroups;

    public function testResult(): void
    {
        $result = new PermissionsAndGroupsCreationResult($this->permissions, $this->permissionGroups);

        $this->assertSame($this->permissions, $result->getCreatedPermissions());
        $this->assertSame($this->permissionGroups, $result->getCreatedPermissionGroups());
    }

    public function testEmptyResult(): void
    {
        $result = new PermissionsAndGroupsCreationResult();

        $this->assertEmpty($result->getCreatedPermissions());
        $this->assertEmpty($result->getCreatedPermissionGroups());
    }

    public function testResultWithInvalidPermissions(): void
    {
        $this->expectException(LogicException::class);
        new PermissionsAndGroupsCreationResult([new stdClass()], $this->permissionGroups);
    }

    public function testResultWithInvalidPermissionGroups(): void
    {
        $this->expectException(LogicException::class);
        new PermissionsAndGroupsCreationResult($this->permissions, [new stdClass()]);
    }

    protected function setUp(): void
    {
        $group1 = new PermissionGroup('group1', 'Group 1', 1);
        $group2 = new PermissionGroup('group2', 'Group 2', 2);

        $permission1 = new Permission('perm1', 'Permission 1', 1, $group1);
        $permission2 = new Permission('perm2', 'Permission 2', 2, $group2);

        $this->permissions = [$permission1, $permission2];
        $this->permissionGroups = [$group1, $group2];
    }
}