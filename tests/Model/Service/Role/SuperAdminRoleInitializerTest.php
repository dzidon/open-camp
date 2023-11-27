<?php

namespace App\Tests\Model\Service\Role;

use App\Model\Entity\Permission;
use App\Model\Repository\RoleRepositoryInterface;
use App\Model\Service\Role\SuperAdminRoleInitializer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SuperAdminRoleInitializerTest extends KernelTestCase
{
    private SuperAdminRoleInitializer $initializer;

    private RoleRepositoryInterface $roleRepository;

    public function testInitializeSuperAdminRole(): void
    {
        $role1 = $this->initializer->initializeSuperAdminRole();
        $role1IdString = $role1->getId()->toRfc4122();
        $permissions = $role1->getPermissions();
        $permissionNames = $this->getPermissionNames($permissions);

        $this->assertSame('Super admin', $role1->getLabel());
        $this->assertCount(4, $permissions);
        $this->assertContains('permission1', $permissionNames);
        $this->assertContains('permission2', $permissionNames);
        $this->assertContains('permission3', $permissionNames);
        $this->assertContains('permission4', $permissionNames);

        $this->roleRepository->saveRole($role1, true);

        $role2 = $this->initializer->initializeSuperAdminRole();
        $role2IdString = $role2->getId()->toRfc4122();

        $this->assertSame($role1IdString, $role2IdString);
    }

    private function getPermissionNames(array $permissions): array
    {
        $names = [];

        /** @var Permission $permission */
        foreach ($permissions as $permission)
        {
            $names[] = $permission->getName();
        }

        return $names;
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        /** @var SuperAdminRoleInitializer $initializer */
        $initializer = $container->get(SuperAdminRoleInitializer::class);
        $this->initializer = $initializer;

        /** @var RoleRepositoryInterface $roleRepository */
        $roleRepository = $container->get(RoleRepositoryInterface::class);
        $this->roleRepository = $roleRepository;
    }
}