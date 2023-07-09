<?php

namespace App\Tests\Form\DataTransfer\Data\Admin;

use App\Form\DataTransfer\Data\Admin\UserData;
use App\Form\DataTransfer\Data\User\BillingData;
use App\Model\Entity\Role;
use App\Model\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserDataTest extends KernelTestCase
{
    public function testId(): void
    {
        $data = new UserData();
        $this->assertSame(null, $data->getId());

        $data->setId(123);
        $this->assertSame(123, $data->getId());

        $data->setId(null);
        $this->assertSame(null, $data->getId());
    }

    public function testEmail(): void
    {
        $data = new UserData();
        $this->assertSame('', $data->getEmail());

        $data->setEmail(null);
        $this->assertSame('', $data->getEmail());

        $data->setEmail('text');
        $this->assertSame('text', $data->getEmail());
    }

    public function testEmailValidation(): void
    {
        $validator = $this->getValidator();

        $data = new UserData();
        $result = $validator->validateProperty($data, 'email');
        $this->assertNotEmpty($result); // invalid

        $data->setEmail(null);
        $result = $validator->validateProperty($data, 'email');
        $this->assertNotEmpty($result); // invalid

        $data->setEmail('abc');
        $result = $validator->validateProperty($data, 'email');
        $this->assertNotEmpty($result); // invalid

        $data->setEmail(str_repeat('x', 177) . '@a.b');
        $result = $validator->validateProperty($data, 'email');
        $this->assertNotEmpty($result); // invalid

        $data->setEmail(str_repeat('x', 176) . '@a.b');
        $result = $validator->validateProperty($data, 'email');
        $this->assertEmpty($result); // valid

        $data->setEmail('abc@gmail.com');
        $result = $validator->validateProperty($data, 'email');
        $this->assertEmpty($result); // valid
    }

    public function testUniqueValidation(): void
    {
        $validator = $this->getValidator();

        $data = new UserData();
        $data->setId(null);
        $data->setEmail('david@gmail.com');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setId(null);
        $data->setEmail('bob@gmail.com');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $userRepository = $this->getUserRepository();
        $user = $userRepository->findOneByEmail('david@gmail.com');
        $data->setId($user->getId());
        $data->setEmail('david@gmail.com');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $data->setId($user->getId());
        $data->setEmail('jeff@gmail.com');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid
    }

    public function testRole(): void
    {
        $data = new UserData();
        $this->assertSame(null, $data->getRole());

        $role = new Role('label');
        $data->setRole($role);
        $this->assertSame($role, $data->getRole());

        $data->setRole(null);
        $this->assertSame(null, $data->getRole());
    }

    public function testBillingData(): void
    {
        $data = new UserData();
        $billingData = $data->getBillingData();
        $this->assertInstanceOf(BillingData::class, $billingData);
    }

    private function getUserRepository(): UserRepositoryInterface
    {
        $container = static::getContainer();

        /** @var UserRepositoryInterface $repository */
        $repository = $container->get(UserRepositoryInterface::class);

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