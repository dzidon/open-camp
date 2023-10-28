<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\UserData;
use App\Library\Data\User\BillingData;
use App\Model\Entity\Role;
use App\Model\Entity\User;
use App\Model\Repository\UserRepositoryInterface;
use libphonenumber\PhoneNumber;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserDataTest extends KernelTestCase
{
    public function testTripLocation(): void
    {
        $data = new UserData(true);
        $this->assertNull($data->getUser());

        $user = new User('bob@gmail.com');

        $data = new UserData(true, $user);
        $this->assertSame($user, $data->getUser());
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
        $data->setEmail('david@gmail.com');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setEmail('bob@gmail.com');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $userRepository = $this->getUserRepository();
        $user = $userRepository->findOneByEmail('david@gmail.com');
        $data = new UserData(true, $user);

        $data->setEmail('david@gmail.com');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $data->setEmail('jeff@gmail.com');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setEmail('bob@gmail.com');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid
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

    public function testPhoneNumber(): void
    {
        $data = new UserData(true);
        $this->assertNull($data->getLeaderPhoneNumber());

        $phoneNumber = new PhoneNumber();
        $phoneNumber->setCountryCode(420);
        $phoneNumber->setNationalNumber('724888999');

        $data->setLeaderPhoneNumber($phoneNumber);
        $this->assertNotSame($phoneNumber, $data->getLeaderPhoneNumber());
        $this->assertSame(420, $data->getLeaderPhoneNumber()->getCountryCode());
        $this->assertSame('724888999', $data->getLeaderPhoneNumber()->getNationalNumber());

        $data->setLeaderPhoneNumber(null);
        $this->assertNull($data->getLeaderPhoneNumber());
    }

    public function testPhoneNumberValidation(): void
    {
        $validator = $this->getValidator();

        $data = new UserData(true);
        $result = $validator->validateProperty($data, 'leaderPhoneNumber');
        $this->assertEmpty($result); // valid

        $data->setLeaderPhoneNumber(null);
        $result = $validator->validateProperty($data, 'leaderPhoneNumber');
        $this->assertEmpty($result); // valid

        $phoneNumber = new PhoneNumber();
        $phoneNumber->setCountryCode(420);
        $phoneNumber->setNationalNumber('123');
        $data->setLeaderPhoneNumber($phoneNumber);
        $result = $validator->validateProperty($data, 'leaderPhoneNumber');
        $this->assertNotEmpty($result); // invalid

        $phoneNumber = new PhoneNumber();
        $phoneNumber->setCountryCode(420);
        $phoneNumber->setNationalNumber('607888999');
        $data->setLeaderPhoneNumber($phoneNumber);
        $result = $validator->validateProperty($data, 'leaderPhoneNumber');
        $this->assertEmpty($result); // valid
    }

    public function testBillingData(): void
    {
        $data = new UserData(true);
        $billingData = $data->getBillingData();
        $this->assertInstanceOf(BillingData::class, $billingData);
    }

    public function testBillingDataValidation(): void
    {
        $validator = $this->getValidator();

        $data = new UserData(true);
        $billingData = $data->getBillingData();
        $billingData->setNameFirst('John');
        $result = $validator->validateProperty($data, 'billingData');
        $this->assertNotEmpty($result); // invalid

        $billingData->setNameLast('Doe');
        $result = $validator->validateProperty($data, 'billingData');
        $this->assertEmpty($result); // valid
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