<?php

namespace App\Tests\Form\DataTransfer\Transfer\Admin;

use App\Form\DataTransfer\Data\Admin\UserData;
use App\Form\DataTransfer\Transfer\Admin\UserDataTransfer;
use App\Model\Entity\Role;
use App\Model\Entity\User;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\SecurityBundle\Security;

class UserDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getUserDataTransfer();

        $expectedEmail = 'abc@gmail.com';
        $expectedRole = new Role('label');
        $expectedName = 'Name';
        $expectedStreet = 'Street';
        $expectedTown = 'Town';
        $expectedZip = 'Zip';
        $expectedCountry = 'Country';

        $user = new User($expectedEmail);
        $user->setRole($expectedRole);
        $user->setName($expectedName);
        $user->setStreet($expectedStreet);
        $user->setTown($expectedTown);
        $user->setZip($expectedZip);
        $user->setCountry($expectedCountry);

        $data = new UserData();
        $dataTransfer->fillData($data, $user);

        $this->assertSame($user->getId(), $data->getId());
        $this->assertSame($expectedEmail, $data->getEmail());
        $this->assertSame($expectedRole, $data->getRole());

        $billingData = $data->getBillingData();
        $this->assertSame($expectedName, $billingData->getName());
        $this->assertSame($expectedStreet, $billingData->getStreet());
        $this->assertSame($expectedTown, $billingData->getTown());
        $this->assertSame($expectedZip, $billingData->getZip());
        $this->assertSame($expectedCountry, $billingData->getCountry());
    }

    public function testFillEntityUserUpdateGranted(): void
    {
        $dataTransfer = $this->getUserDataTransfer(['user_update']);

        $expectedEmail = 'abc@gmail.com';
        $expectedRole = new Role('label');
        $expectedName = 'Name';
        $expectedStreet = 'Street';
        $expectedTown = 'Town';
        $expectedZip = 'Zip';
        $expectedCountry = 'Country';

        $data = new UserData();
        $data->setEmail($expectedEmail);
        $data->setRole($expectedRole);

        $billingData = $data->getBillingData();
        $billingData->setName($expectedName);
        $billingData->setStreet($expectedStreet);
        $billingData->setTown($expectedTown);
        $billingData->setZip($expectedZip);
        $billingData->setCountry($expectedCountry);

        $user = new User('bob@gmail.com');
        $dataTransfer->fillEntity($data, $user);

        $this->assertNotSame($user->getId(), $data->getId());
        $this->assertSame($expectedEmail, $user->getEmail());
        $this->assertNull($user->getRole());
        $this->assertSame($expectedName, $user->getName());
        $this->assertSame($expectedStreet, $user->getStreet());
        $this->assertSame($expectedTown, $user->getTown());
        $this->assertSame($expectedZip, $user->getZip());
        $this->assertSame($expectedCountry, $user->getCountry());
    }

    public function testFillEntityUserUpdateRoleGranted(): void
    {
        $dataTransfer = $this->getUserDataTransfer(['user_update_role']);

        $expectedEmail = 'abc@gmail.com';
        $expectedRole = new Role('label');
        $expectedName = 'Name';
        $expectedStreet = 'Street';
        $expectedTown = 'Town';
        $expectedZip = 'Zip';
        $expectedCountry = 'Country';

        $data = new UserData();
        $data->setEmail($expectedEmail);
        $data->setRole($expectedRole);

        $billingData = $data->getBillingData();
        $billingData->setName($expectedName);
        $billingData->setStreet($expectedStreet);
        $billingData->setTown($expectedTown);
        $billingData->setZip($expectedZip);
        $billingData->setCountry($expectedCountry);

        $user = new User('bob@gmail.com');
        $dataTransfer->fillEntity($data, $user);

        $this->assertNotSame($user->getId(), $data->getId());
        $this->assertSame('bob@gmail.com', $user->getEmail());
        $this->assertSame($expectedRole, $user->getRole());
        $this->assertNull($user->getName());
        $this->assertNull($user->getStreet());
        $this->assertNull($user->getTown());
        $this->assertNull($user->getZip());
        $this->assertNull($user->getCountry());
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