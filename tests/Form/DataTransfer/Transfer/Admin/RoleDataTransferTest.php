<?php

namespace App\Tests\Form\DataTransfer\Transfer\Admin;

use App\Entity\Permission;
use App\Entity\PermissionGroup;
use App\Entity\Role;
use App\Form\DataTransfer\Data\Admin\RoleData;
use App\Form\DataTransfer\Registry\DataTransferRegistryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RoleDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $registry = $this->getDataTransferRegistry();

        $permissions = [
            new Permission('x', 'y', 1, new PermissionGroup('x', 'y', 1)),
            new Permission('x', 'y', 1, new PermissionGroup('x', 'y', 1)),
        ];

        $data = new RoleData();
        $role = new Role('abc');
        foreach ($permissions as $permission)
        {
            $role->addPermission($permission);
        }

        $registry->fillData($data, $role);
        $this->assertSame('abc', $data->getLabel());
        $this->assertSame($permissions, $data->getPermissions());
    }

    public function testFillEntity(): void
    {
        $registry = $this->getDataTransferRegistry();

        $permissions = [
            new Permission('x', 'y', 1, new PermissionGroup('x', 'y', 1)),
            new Permission('x', 'y', 1, new PermissionGroup('x', 'y', 1)),
        ];

        $role = new Role('abc');
        $data = new RoleData();
        $data->setLabel('xyz');
        $data->setPermissions($permissions);

        $registry->fillEntity($data, $role);
        $this->assertSame('xyz', $role->getLabel());
        $this->assertSame($permissions, $role->getPermissions());
    }

    private function getDataTransferRegistry(): DataTransferRegistryInterface
    {
        $container = static::getContainer();

        /** @var DataTransferRegistryInterface $registry */
        $registry = $container->get(DataTransferRegistryInterface::class);

        return $registry;
    }
}