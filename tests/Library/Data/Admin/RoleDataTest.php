<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\RoleData;
use App\Model\Entity\Permission;
use App\Model\Entity\PermissionGroup;
use App\Model\Repository\RoleRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RoleDataTest extends KernelTestCase
{
    public function testId(): void
    {
        $data = new RoleData();
        $this->assertNull($data->getId());

        $uid = Uuid::v4();
        $data->setId($uid);
        $this->assertSame($uid, $data->getId());

        $data->setId(null);
        $this->assertNull($data->getId());
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
        $data->setId(null);
        $data->setLabel('Super admin');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setId(null);
        $data->setLabel('text');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $roleRepository = $this->getRoleRepository();
        $role = $roleRepository->findOneByLabel('Admin');
        $data->setId($role->getId());
        $data->setLabel('Admin');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $data->setId($role->getId());
        $data->setLabel('Super admin');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid
    }

    public function testPermissions(): void
    {
        $data = new RoleData();
        $this->assertEmpty($data->getPermissions());

        $permissions = [
            new Permission('x', 'X', 1, new PermissionGroup('group', 'Group', 1)),
            new Permission('y', 'Y', 1, new PermissionGroup('group', 'Group', 1)),
        ];

        $data->setPermissions($permissions);
        $this->assertSame($permissions, $data->getPermissions());
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