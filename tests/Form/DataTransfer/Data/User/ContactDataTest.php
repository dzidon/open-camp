<?php

namespace App\Tests\Form\DataTransfer\Data\User;

use App\Form\DataTransfer\Data\User\ContactData;
use libphonenumber\PhoneNumber;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContactDataTest extends KernelTestCase
{
    public function testName(): void
    {
        $data = new ContactData();
        $this->assertNull($data->getName());

        $data->setName('text');
        $this->assertSame('text', $data->getName());

        $data->setName(null);
        $this->assertNull($data->getName());
    }

    public function testNameValidation(): void
    {
        $validator = $this->getValidator();

        $data = new ContactData();
        $result = $validator->validateProperty($data, 'name');
        $this->assertNotEmpty($result); // invalid

        $data->setName('');
        $result = $validator->validateProperty($data, 'name');
        $this->assertNotEmpty($result); // invalid

        $data->setName(null);
        $result = $validator->validateProperty($data, 'name');
        $this->assertNotEmpty($result); // invalid

        $data->setName(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'name');
        $this->assertEmpty($result); // valid

        $data->setName(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'name');
        $this->assertNotEmpty($result); // invalid
    }

    public function testEmail(): void
    {
        $data = new ContactData();
        $this->assertNull($data->getEmail());

        $data->setEmail('text');
        $this->assertSame('text', $data->getEmail());

        $data->setEmail(null);
        $this->assertNull($data->getEmail());
    }

    public function testEmailValidation(): void
    {
        $validator = $this->getValidator();

        $data = new ContactData();
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

    public function testPhoneNumber(): void
    {
        $data = new ContactData();
        $this->assertNull($data->getPhoneNumber());

        $phoneNumber = new PhoneNumber();
        $data->setPhoneNumber($phoneNumber);
        $this->assertSame($phoneNumber, $data->getPhoneNumber());

        $data->setPhoneNumber(null);
        $this->assertNull($data->getPhoneNumber());
    }

    public function testPhoneNumberValidation(): void
    {
        $validator = $this->getValidator();

        $data = new ContactData();
        $result = $validator->validateProperty($data, 'phoneNumber');
        $this->assertNotEmpty($result); // invalid

        $phoneNumber = new PhoneNumber();
        $phoneNumber->setCountryCode(420);
        $phoneNumber->setNationalNumber('123');
        $result = $validator->validateProperty($data, 'phoneNumber');
        $this->assertNotEmpty($result); // invalid

        $phoneNumber = new PhoneNumber();
        $phoneNumber->setCountryCode(420);
        $phoneNumber->setNationalNumber('607888999');
        $data->setPhoneNumber($phoneNumber);
        $result = $validator->validateProperty($data, 'phoneNumber');
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