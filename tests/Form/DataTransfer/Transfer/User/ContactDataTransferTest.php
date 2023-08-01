<?php

namespace App\Tests\Form\DataTransfer\Transfer\User;

use App\Enum\Entity\ContactRoleEnum;
use App\Form\DataTransfer\Data\User\ContactData;
use App\Form\DataTransfer\Transfer\User\ContactDataTransfer;
use App\Model\Entity\Contact;
use App\Model\Entity\User;
use libphonenumber\PhoneNumber;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContactDataTransferTest extends KernelTestCase
{
    public function testFillData(): void
    {
        $dataTransfer = $this->getContactDataTransfer();

        $expectedNameFirst = 'John';
        $expectedNameLast = 'Doe';
        $expectedEmail = 'test@test.com';
        $expectedPhoneNumber = new PhoneNumber();
        $expectedPhoneNumber->setCountryCode(420);
        $expectedPhoneNumber->setNationalNumber('724888999');
        $expectedRole = ContactRoleEnum::MOTHER;

        $user = new User('bob@gmail.com');
        $contact = new Contact($expectedNameFirst, $expectedNameLast, $expectedRole, $user);
        $contact->setEmail($expectedEmail);
        $contact->setPhoneNumber($expectedPhoneNumber);

        $data = new ContactData();
        $dataTransfer->fillData($data, $contact);

        $this->assertSame($expectedNameFirst, $data->getNameFirst());
        $this->assertSame($expectedNameLast, $data->getNameLast());
        $this->assertSame($expectedEmail, $data->getEmail());
        $this->assertSame($expectedRole, $data->getRole());

        $phoneNumber = $data->getPhoneNumber();
        $this->assertSame(420, $phoneNumber->getCountryCode());
        $this->assertSame('724888999', $phoneNumber->getNationalNumber());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getContactDataTransfer();

        $expectedNameFirst = 'John';
        $expectedNameLast = 'Doe';
        $expectedEmail = 'test@test.com';
        $expectedPhoneNumber = new PhoneNumber();
        $expectedPhoneNumber->setCountryCode(420);
        $expectedPhoneNumber->setNationalNumber('724888999');
        $expectedRole = ContactRoleEnum::MOTHER;

        $user = new User('bob@gmail.com');
        $contact = new Contact('', '', ContactRoleEnum::TUTOR, $user);

        $data = new ContactData();
        $data->setNameFirst($expectedNameFirst);
        $data->setNameLast($expectedNameLast);
        $data->setEmail($expectedEmail);
        $data->setPhoneNumber($expectedPhoneNumber);
        $data->setRole($expectedRole);

        $dataTransfer->fillEntity($data, $contact);

        $this->assertSame($expectedNameFirst, $contact->getNameFirst());
        $this->assertSame($expectedNameLast, $contact->getNameLast());
        $this->assertSame($expectedEmail, $contact->getEmail());
        $this->assertSame($expectedRole, $contact->getRole());

        $phoneNumber = $contact->getPhoneNumber();
        $this->assertSame(420, $phoneNumber->getCountryCode());
        $this->assertSame('724888999', $phoneNumber->getNationalNumber());
    }

    private function getContactDataTransfer(): ContactDataTransfer
    {
        $container = static::getContainer();

        /** @var ContactDataTransfer $dataTransfer */
        $dataTransfer = $container->get(ContactDataTransfer::class);

        return $dataTransfer;
    }
}