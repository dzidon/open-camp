<?php

namespace App\Tests\Form\DataTransfer\Transfer\User;

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

        $expectedName = 'Name';
        $expectedEmail = 'test@test.com';
        $expectedPhoneNumber = new PhoneNumber();

        $user = new User('bob@gmail.com');
        $contact = new Contact($expectedName, $expectedEmail, $expectedPhoneNumber, $user);

        $data = new ContactData();
        $dataTransfer->fillData($data, $contact);

        $this->assertSame($expectedName, $data->getName());
        $this->assertSame($expectedEmail, $data->getEmail());
        $this->assertSame($expectedPhoneNumber, $data->getPhoneNumber());
    }

    public function testFillEntity(): void
    {
        $dataTransfer = $this->getContactDataTransfer();

        $expectedName = 'Name';
        $expectedEmail = 'test@test.com';
        $expectedPhoneNumber = new PhoneNumber();

        $user = new User('bob@gmail.com');
        $contact = new Contact('', '', new PhoneNumber(), $user);

        $data = new ContactData();
        $data->setName($expectedName);
        $data->setEmail($expectedEmail);
        $data->setPhoneNumber($expectedPhoneNumber);

        $dataTransfer->fillEntity($data, $contact);

        $this->assertSame($expectedName, $contact->getName());
        $this->assertSame($expectedEmail, $contact->getEmail());
        $this->assertSame($expectedPhoneNumber, $contact->getPhoneNumber());
    }

    private function getContactDataTransfer(): ContactDataTransfer
    {
        $container = static::getContainer();

        /** @var ContactDataTransfer $dataTransfer */
        $dataTransfer = $container->get(ContactDataTransfer::class);

        return $dataTransfer;
    }
}