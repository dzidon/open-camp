<?php

namespace App\Tests\Entity;

use App\Entity\Permission;
use App\Entity\PermissionGroup;
use PHPUnit\Framework\TestCase;

class PermissionTest extends TestCase
{
    private const NAME = 'name';
    private const LABEL = 'Name...';
    private const PRIORITY = 123;

    private Permission $permission;
    private PermissionGroup $permissionGroup;

    public function testPermissionGroup(): void
    {
        $this->assertSame($this->permissionGroup, $this->permission->getPermissionGroup());

        $permissionGroupNew = new PermissionGroup('xyz', 'Xyz...', 500);
        $this->permission->setPermissionGroup($permissionGroupNew);

        $this->assertSame($permissionGroupNew, $this->permission->getPermissionGroup());
    }

    public function testName(): void
    {
        $this->assertSame(self::NAME, $this->permission->getName());

        $newName = 'new_name';
        $this->permission->setName($newName);
        $this->assertSame($newName, $this->permission->getName());
    }

    public function testLabel(): void
    {
        $this->assertSame(self::LABEL, $this->permission->getLabel());

        $newLabel = 'New label';
        $this->permission->setLabel($newLabel);
        $this->assertSame($newLabel, $this->permission->getLabel());
    }

    public function testPriority(): void
    {
        $this->assertSame(self::PRIORITY, $this->permission->getPriority());

        $newPriority = 321;
        $this->permission->setPriority($newPriority);
        $this->assertSame($newPriority, $this->permission->getPriority());
    }

    protected function setUp(): void
    {
        $this->permissionGroup = new PermissionGroup('group_name', 'Group name...', 321);
        $this->permission = new Permission(self::NAME, self::LABEL, self::PRIORITY, $this->permissionGroup);
    }
}