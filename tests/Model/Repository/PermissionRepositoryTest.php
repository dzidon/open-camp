<?php

namespace App\Tests\Model\Repository;

use App\Model\Entity\Permission;
use App\Model\Entity\PermissionGroup;
use App\Model\Repository\PermissionGroupRepositoryInterface;
use App\Model\Repository\PermissionRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests the Permission repository.
 */
class PermissionRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $permissionRepository = $this->getPermissionRepository();
        $permissionGroupRepository = $this->getPermissionGroupRepository();

        $group = new PermissionGroup('new_group', 'New group', 1);
        $permission = new Permission('new_permission', 'New permission', 1, $group);

        $permissionGroupRepository->savePermissionGroup($group, false);
        $permissionRepository->savePermission($permission, true);
        $id = $permission->getId();

        $loadedPermission = $permissionRepository->find($id);
        $this->assertNotNull($loadedPermission);
        $this->assertSame($permission->getId(), $loadedPermission->getId());

        $permissionRepository->removePermission($permission, true);
        $loadedPermission = $permissionRepository->find($id);
        $this->assertNull($loadedPermission);
    }

    public function testCreate(): void
    {
        $permissionRepository = $this->getPermissionRepository();
        $group = new PermissionGroup('new_group', 'New group', 1);

        $permission = $permissionRepository->createPermission('new_permission', 'New permission', 1, $group);
        $this->assertSame('new_permission', $permission->getName());
        $this->assertSame('New permission', $permission->getLabel());
        $this->assertSame(1, $permission->getPriority());
        $this->assertSame($group, $permission->getPermissionGroup());
    }

    public function testFindAll(): void
    {
        $permissionRepository = $this->getPermissionRepository();
        $permissions = $permissionRepository->findAll();

        $names = [];
        foreach ($permissions as $group)
        {
            $names[] = $group->getName();
        }

        $this->assertSame(['permission1', 'permission2', 'permission3', 'permission4'], $names);
    }

    private function getPermissionRepository(): PermissionRepository
    {
        $container = static::getContainer();

        /** @var PermissionRepository $repository */
        $repository = $container->get(PermissionRepository::class);

        return $repository;
    }

    private function getPermissionGroupRepository(): PermissionGroupRepositoryInterface
    {
        $container = static::getContainer();

        /** @var PermissionGroupRepositoryInterface $repository */
        $repository = $container->get(PermissionGroupRepositoryInterface::class);

        return $repository;
    }
}