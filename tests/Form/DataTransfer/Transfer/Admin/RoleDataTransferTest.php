<?php

namespace App\Tests\Form\DataTransfer\Transfer\Admin;

use App\Form\DataTransfer\Data\Admin\RoleData;
use App\Form\DataTransfer\Transfer\Admin\RoleDataTransfer;
use App\Model\Entity\Permission;
use App\Model\Entity\PermissionGroup;
use App\Model\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RoleDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getRoleDataTransfer();

        $permissions = [
            new Permission('x', 'y', 1, new PermissionGroup('x', 'y', 1)),
            new Permission('x', 'y', 1, new PermissionGroup('x', 'y', 1)),
        ];

        $role = new Role('abc');
        foreach ($permissions as $permission)
        {
            $role->addPermission($permission);
        }

        $data = new RoleData();
        $dataTransfer->fillData($data, $role);
        $this->assertSame('abc', $data->getLabel());
        $this->assertSame($permissions, $data->getPermissions());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getRoleDataTransfer();

        $permissions = [
            new Permission('x', 'y', 1, new PermissionGroup('x', 'y', 1)),
            new Permission('x', 'y', 1, new PermissionGroup('x', 'y', 1)),
        ];

        $role = new Role('abc');
        $data = new RoleData();
        $data->setLabel('xyz');
        $data->setPermissions($permissions);

        $dataTransfer->fillEntity($data, $role);
        $this->assertSame('xyz', $role->getLabel());
        $this->assertSame($permissions, $role->getPermissions());
    }

    private function getRoleDataTransfer(): RoleDataTransfer
    {
        $container = static::getContainer();

        /** @var RoleDataTransfer $dataTransfer */
        $dataTransfer = $container->get(RoleDataTransfer::class);

        return $dataTransfer;
    }
}