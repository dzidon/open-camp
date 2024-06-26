<?php

namespace App\Tests\Library\Data\Common;

use App\Library\Data\Common\ContactData;
use App\Model\Enum\Entity\ContactRoleEnum;
use libphonenumber\PhoneNumber;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContactDataTest extends KernelTestCase
{
    public function testEmailAndPhoneNumberMandatory(): void
    {
        $data = new ContactData(false, false);
        $this->assertFalse($data->isEmailMandatory());
        $this->assertFalse($data->isPhoneNumberMandatory());

        $data = new ContactData(true, true);
        $this->assertTrue($data->isEmailMandatory());
        $this->assertTrue($data->isPhoneNumberMandatory());
    }

    public function testNameFirst(): void
    {
        $data = new ContactData(false, false);
        $this->assertNull($data->getNameFirst());

        $data->setNameFirst('text');
        $this->assertSame('text', $data->getNameFirst());

        $data->setNameFirst(null);
        $this->assertNull($data->getNameFirst());
    }

    public function testNameFirstValidation(): void
    {
        $validator = $this->getValidator();

        $data = new ContactData(false, false);
        $result = $validator->validateProperty($data, 'nameFirst');
        $this->assertNotEmpty($result); // invalid

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
        $data = new ContactData(false, false);
        $this->assertNull($data->getNameLast());

        $data->setNameLast('text');
        $this->assertSame('text', $data->getNameLast());

        $data->setNameLast(null);
        $this->assertNull($data->getNameLast());
    }

    public function testNameLastValidation(): void
    {
        $validator = $this->getValidator();

        $data = new ContactData(false, false);
        $result = $validator->validateProperty($data, 'nameLast');
        $this->assertNotEmpty($result); // invalid

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

    public function testEmail(): void
    {
        $data = new ContactData(false, false);
        $this->assertNull($data->getEmail());

        $data->setEmail('text');
        $this->assertSame('text', $data->getEmail());

        $data->setEmail(null);
        $this->assertNull($data->getEmail());
    }

    public function testEmailValidation(): void
    {
        $validator = $this->getValidator();

        $data = new ContactData(false, false);
        $data->setNameFirst('Name');
        $data->setNameLast('Surname');
        $data->setRole(ContactRoleEnum::FATHER);

        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setEmail('');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setEmail(null);
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setEmail('abc');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setEmail(str_repeat('x', 177) . '@a.b');
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setEmail(str_repeat('x', 176) . '@a.b');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid

        $data->setEmail('abc@gmail.com');
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid
    }

    public function testPhoneNumber(): void
    {
        $data = new ContactData(false, false);
        $this->assertNull($data->getPhoneNumber());

        $phoneNumber = new PhoneNumber();
        $phoneNumber->setCountryCode(420);
        $phoneNumber->setNationalNumber('724888999');

        $data->setPhoneNumber($phoneNumber);
        $this->assertNotSame($phoneNumber, $data->getPhoneNumber());
        $this->assertSame(420, $data->getPhoneNumber()->getCountryCode());
        $this->assertSame('724888999', $data->getPhoneNumber()->getNationalNumber());

        $data->setPhoneNumber(null);
        $this->assertNull($data->getPhoneNumber());
    }

    public function testPhoneNumberValidation(): void
    {
        $validator = $this->getValidator();

        $data = new ContactData(false, false);
        $data->setNameFirst('Name');
        $data->setNameLast('Surname');
        $data->setRole(ContactRoleEnum::FATHER);

        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setPhoneNumber(null);
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $phoneNumber = new PhoneNumber();
        $phoneNumber->setCountryCode(420);
        $phoneNumber->setNationalNumber('123');
        $data->setPhoneNumber($phoneNumber);
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $phoneNumber = new PhoneNumber();
        $phoneNumber->setCountryCode(420);
        $phoneNumber->setNationalNumber('607888999');
        $data->setPhoneNumber($phoneNumber);
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid
    }

    public function testEmailAndPhoneNumberValidationIfMandatory(): void
    {
        $validator = $this->getValidator();

        $data = new ContactData(true, true);
        $data->setNameFirst('Name');
        $data->setNameLast('Surname');
        $data->setRole(ContactRoleEnum::FATHER);

        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setEmail('bob@gmail.com');
        $data->setPhoneNumber(null);
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setEmail(null);
        $phoneNumber = new PhoneNumber();
        $phoneNumber->setCountryCode(420);
        $phoneNumber->setNationalNumber('607888999');
        $data->setPhoneNumber($phoneNumber);
        $result = $validator->validate($data);
        $this->assertNotEmpty($result); // invalid

        $data->setEmail('bob@gmail.com');
        $data->setPhoneNumber($phoneNumber);
        $result = $validator->validate($data);
        $this->assertEmpty($result); // valid
    }

    public function testRole(): void
    {
        $data = new ContactData(false, false);
        $this->assertNull($data->getRole());

        $data->setRole(ContactRoleEnum::MOTHER);
        $this->assertSame(ContactRoleEnum::MOTHER, $data->getRole());

        $data->setRole(null);
        $this->assertNull($data->getRole());
    }

    public function testRoleValidation(): void
    {
        $validator = $this->getValidator();

        $data = new ContactData(false, false);
        $result = $validator->validateProperty($data, 'role');
        $this->assertNotEmpty($result); // invalid

        $data->setRole(ContactRoleEnum::MOTHER);
        $result = $validator->validateProperty($data, 'role');
        $this->assertEmpty($result); // valid

        $data->setRole(null);
        $result = $validator->validateProperty($data, 'role');
        $this->assertNotEmpty($result); // invalid
    }

    public function testRoleOther(): void
    {
        $data = new ContactData(false, false);
        $this->assertNull($data->getRoleOther());

        $data->setRoleOther('Other role');
        $this->assertSame('Other role', $data->getRoleOther());

        $data->setRoleOther(null);
        $this->assertNull($data->getRoleOther());
    }

    public function testRoleOtherValidation(): void
    {
        $validator = $this->getValidator();

        $data = new ContactData(false, false);
        $result = $validator->validateProperty($data, 'roleOther');
        $this->assertEmpty($result); // valid

        $data->setRoleOther(null);
        $result = $validator->validateProperty($data, 'roleOther');
        $this->assertEmpty($result); // valid

        $data->setRoleOther('');
        $result = $validator->validateProperty($data, 'roleOther');
        $this->assertEmpty($result); // valid

        $data->setRoleOther('Other role');
        $result = $validator->validateProperty($data, 'roleOther');
        $this->assertEmpty($result); // valid

        $data->setRole(ContactRoleEnum::OTHER);
        $data->setRoleOther(null);
        $result = $validator->validateProperty($data, 'roleOther');
        $this->assertNotEmpty($result); // invalid

        $data->setRoleOther('');
        $result = $validator->validateProperty($data, 'roleOther');
        $this->assertNotEmpty($result); // invalid

        $data->setRoleOther(str_repeat('x', 255));
        $result = $validator->validateProperty($data, 'roleOther');
        $this->assertEmpty($result); // valid

        $data->setRoleOther(str_repeat('x', 256));
        $result = $validator->validateProperty($data, 'roleOther');
        $this->assertNotEmpty($result); // invalid
    }

    private function getValidator(): ValidatorInterface
    {
        $container = static::getContainer();

        /** @var ValidatorInterface $validator */
        $validator = $container->get(ValidatorInterface::class);

        return $validator;
    }
}