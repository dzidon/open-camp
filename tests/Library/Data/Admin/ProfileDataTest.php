<?php

namespace App\Tests\Library\Data\Admin;

use App\Library\Data\Admin\ProfileData;
use libphonenumber\PhoneNumber;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProfileDataTest extends KernelTestCase
{
    public function testNameFirst(): void
    {
        $data = new ProfileData();
        $this->assertNull($data->getNameFirst());

        $data->setNameFirst('text');
        $this->assertSame('text', $data->getNameFirst());

        $data->setNameFirst(null);
        $this->assertNull($data->getNameFirst());
    }

    public function testNameFirstValidation(): void
    {
        $validator = $this->getValidator();

        $data = new ProfileData();
        $result = $validator->validateProperty($data, 'nameFirst');
        $this->assertEmpty($result); // valid

        $data->setNameFirst('');
        $result = $validator->validateProperty($data, 'nameFirst');
        $this->assertEmpty($result); // valid

        $data->setNameFirst(null);
        $result = $validator->validateProperty($data, 'nameFirst');
        $this->assertEmpty($result); // valid

        $data->setNameLast('Doe');
        $data->setNameFirst('');
        $result = $validator->validateProperty($data, 'nameFirst');
        $this->assertNotEmpty($result); // invalid

        $data->setNameFirst(null);
        $result = $validator->validateProperty($data, 'nameFirst');
        $this->assertNotEmpty($result); // invalid

        $data->setNameFirst(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'nameFirst');
        $this->assertEmpty($result); // valid

        $data->setNameFirst(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'nameFirst');
        $this->assertNotEmpty($result); // invalid
    }

    public function testNameLast(): void
    {
        $data = new ProfileData();
        $this->assertNull($data->getNameLast());

        $data->setNameLast('text');
        $this->assertSame('text', $data->getNameLast());

        $data->setNameLast(null);
        $this->assertNull($data->getNameLast());
    }

    public function testNameLastValidation(): void
    {
        $validator = $this->getValidator();

        $data = new ProfileData();
        $result = $validator->validateProperty($data, 'nameLast');
        $this->assertEmpty($result); // valid

        $data->setNameLast('');
        $result = $validator->validateProperty($data, 'nameLast');
        $this->assertEmpty($result); // valid

        $data->setNameLast(null);
        $result = $validator->validateProperty($data, 'nameLast');
        $this->assertEmpty($result); // valid

        $data->setNameFirst('John');
        $data->setNameLast('');
        $result = $validator->validateProperty($data, 'nameLast');
        $this->assertNotEmpty($result); // invalid

        $data->setNameLast(null);
        $result = $validator->validateProperty($data, 'nameLast');
        $this->assertNotEmpty($result); // invalid

        $data->setNameLast(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'nameLast');
        $this->assertEmpty($result); // valid

        $data->setNameLast(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'nameLast');
        $this->assertNotEmpty($result); // invalid
    }

    public function testLeaderPhoneNumber(): void
    {
        $data = new ProfileData();
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

    public function testLeaderPhoneNumberValidation(): void
    {
        $validator = $this->getValidator();

        $data = new ProfileData();
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

    private function getValidator(): ValidatorInterface
    {
        $container = static::getContainer();

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);

        return $validator;
    }
}