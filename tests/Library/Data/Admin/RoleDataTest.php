<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\RoleData;
use App\Model\Entity\Permission;
use App\Model\Entity\PermissionGroup;
use App\Model\Entity\Role;
use App\Model\Repository\RoleRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RoleDataTest extends KernelTestCase
{
    public function testRole(): void
    {
        $data = new RoleData();
        $this->assertNull($data->getRole());

        $role = new Role('Role');

        $data = new RoleData($role);
        $this->assertSame($role, $data->getRole());
    }

    public function testLabel(): void
    {
        $data = new RoleData();
        $this->assertNull($data->getLabel());

        $data->setLabel('text');
        $this->assertSame('text', $data->getLabel());

        $data->setLabel(null);
        $this->assertNull($data->getLabel());
    }

    public function testLabelValidation(): void
    {
        $validator = $this->getValidator();

        $data = new RoleData();
        $result = $validator->validateProperty($data, 'label');
        $this->assertNotEmpty($result); // invalid

        $data->setLabel('');
        $result = $validator->validateProperty($data, 'label');
        $this->assertNotEmpty($result); // invalid

        $data->setLabel(null);
        $result = $validator->validateProperty($data, 'label');
        $this->assertNotEmpty($result); // invalid

        $data->setLabel(str_repeat('x', 64));
        $result = $validator->validateProperty($data, 'label');
        $this->assertEmpty($result); // valid

        $data->setLabel(str_repeat('x', 65));
        $result = $validator->validateProperty($data, 'label');
        $this->assertNotEmpty($result); // invalid
    }

    public function testUniqueValidation(): void
    {
        $validator = $this->getValidator();

        $data = new RoleData();
        $data->setLabel('Super admin');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setLabel('text');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $roleRepository = $this->getRoleRepository();
        $role = $roleRepository->findOneByLabel('Admin');

        $data = new RoleData($role);
        $data->setLabel('Admin');

        $data->setLabel('Admin');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $data->setLabel('Super admin');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setLabel('text');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid
    }

    public function testPermissions(): void
    {
        $data = new RoleData();
        $this->assertEmpty($data->getPermissions());

        $permissions = [
            new Permission('x', 'X', 1, new PermissionGroup('group', 'Group', 1)),
            new Permission('y', 'Y', 1, new PermissionGroup('group', 'Group', 1)),
        ];

        foreach ($permissions as $permission)
        {
            $data->addPermission($permission);
        }

        $this->assertSame($permissions, $data->getPermissions());

        $data->removePermission($permissions[0]);
        $this->assertNotContains($permissions[0], $data->getPermissions());
    }

    private function getRoleRepository(): RoleRepositoryInterface
    {
        $container = static::getContainer();

        /** @var RoleRepositoryInterface $repository */
        $repository = $container->get(RoleRepositoryInterface::class);

        return $repository;
    }

    private function getValidator(): ValidatorInterface
    {
        $container = static::getContainer();

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);

        return $validator;
    }
}