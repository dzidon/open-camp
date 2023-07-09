<?php

namespace App\Tests\Form\DataTransfer\Data\Admin;

use App\Form\DataTransfer\Data\Admin\RoleData;
use App\Model\Entity\Permission;
use App\Model\Entity\PermissionGroup;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RoleDataTest extends KernelTestCase
{
    public function testLabel(): void
    {
        $data = new RoleData();
        $this->assertSame('', $data->getLabel());

        $data->setLabel(null);
        $this->assertSame('', $data->getLabel());

        $data->setLabel('text');
        $this->assertSame('text', $data->getLabel());
    }

    public function testLabelValidation(): void
    {
        $validator = $this->getValidator();

        $data = new RoleData();
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

    private function getValidator(): ValidatorInterface
    {
        $container = static::getContainer();

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);

        return $validator;
    }
}