<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\ProfileData;
use App\Library\Data\Admin\UserData;
use App\Library\Data\User\BillingData;
use App\Model\Entity\Role;
use App\Model\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserDataTest extends KernelTestCase
{
    public function testId(): void
    {
        $data = new UserData(true);
        $this->assertNull($data->getId());

        $uid = Uuid::v4();
        $data->setId($uid);
        $this->assertSame($uid, $data->getId());

        $data->setId(null);
        $this->assertNull($data->getId());
    }

    public function testEmail(): void
    {
        $data = new UserData(true);
        $this->assertNull($data->getEmail());

        $data->setEmail('text');
        $this->assertSame('text', $data->getEmail());

        $data->setEmail(null);
        $this->assertNull($data->getEmail());
    }

    public function testEmailValidation(): void
    {
        $validator = $this->getValidator();

        $data = new UserData(true);
        $result = $validator->validateProperty($data, 'email');
        $this->assertNotEmpty($result); // invalid

        $data->setEmail('');
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

        $data = new UserData(true);
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
        $data = new UserData(true);
        $this->assertSame(null, $data->getRole());

        $role = new Role('label');
        $data->setRole($role);
        $this->assertSame($role, $data->getRole());

        $data->setRole(null);
        $this->assertSame(null, $data->getRole());
    }

    public function testBillingData(): void
    {
        $data = new UserData(true);
        $billingData = $data->getBillingData();
        $this->assertInstanceOf(BillingData::class, $billingData);
    }

    public function testUserData(): void
    {
        $data = new UserData(true);
        $profileData = $data->getProfileData();
        $this->assertInstanceOf(ProfileData::class, $profileData);
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