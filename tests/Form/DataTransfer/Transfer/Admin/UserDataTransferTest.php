<?php

namespace App\Tests\Form\DataTransfer\Transfer\Admin;

use App\Entity\Role;
use App\Entity\User;
use App\Form\DataTransfer\Data\Admin\UserData;
use App\Form\DataTransfer\Transfer\Admin\UserDataTransfer;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\SecurityBundle\Security;

class UserDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getUserDataTransfer();

        $expectedId = 123;
        $expectedEmail = 'abc@gmail.com';
        $expectedRole = new Role('label');
        $user = new User($expectedEmail);
        $user->setRole($expectedRole);

        $reflectionClass = new ReflectionClass(User::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($user, $expectedId);

        $data = new UserData();
        $dataTransfer->fillData($data, $user);

        $this->assertSame($expectedId, $data->getId());
        $this->assertSame($expectedEmail, $data->getEmail());
        $this->assertSame($expectedRole, $data->getRole());
    }

    public function testFillEntityUserUpdateGranted(): void
    {
        $dataTransfer = $this->getUserDataTransfer(['user_update']);

        $expectedId = 123;
        $expectedEmail = 'abc@gmail.com';
        $expectedRole = new Role('label');
        $data = new UserData();
        $data->setId($expectedId);
        $data->setEmail($expectedEmail);
        $data->setRole($expectedRole);

        $user = new User('bob@gmail.com');
        $dataTransfer->fillEntity($data, $user);

        $this->assertSame(null, $user->getId());
        $this->assertSame($expectedEmail, $user->getEmail());
        $this->assertSame(null, $user->getRole());
    }

    public function testFillEntityUserUpdateRoleGranted(): void
    {
        $dataTransfer = $this->getUserDataTransfer(['user_update_role']);

        $expectedId = 123;
        $expectedEmail = 'abc@gmail.com';
        $expectedRole = new Role('label');
        $data = new UserData();
        $data->setId($expectedId);
        $data->setEmail($expectedEmail);
        $data->setRole($expectedRole);

        $user = new User('bob@gmail.com');
        $dataTransfer->fillEntity($data, $user);

        $this->assertSame(null, $user->getId());
        $this->assertSame('bob@gmail.com', $user->getEmail());
        $this->assertSame($expectedRole, $user->getRole());
    }

    private function getUserDataTransfer(array $userRoles = []): UserDataTransfer
    {
        $container = static::getContainer();

        /** @var Security|MockObject $securityMock */
        $securityMock = $this->getMockBuilder(Security::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $securityMock
            ->expects($this->any())
            ->method('isGranted')
            ->willReturnCallback(function (string $role) use ($userRoles)
            {
                return in_array($role, $userRoles);
            })
        ;

        $container->set(Security::class, $securityMock);

        /** @var UserDataTransfer $dataTransfer */
        $dataTransfer = $container->get(UserDataTransfer::class);

        return $dataTransfer;
    }
}