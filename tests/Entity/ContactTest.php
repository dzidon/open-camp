<?php

namespace App\Tests\Entity;

use App\Entity\Contact;
use App\Entity\User;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ContactTest extends KernelTestCase
{
    private const NAME = 'Test Contact';
    private const EMAIL = 'test@gmail.com';
    private const PHONE_NUMBER = '+420 724 888 999';

    private Contact $contact;
    private User $user;

    private PhoneNumberUtil $phoneNumberUtil;

    public function testUser(): void
    {
        $this->assertSame($this->contact->getUser(), $this->user);

        $userNew = new User('userNew@test.com');
        $this->contact->setUser($userNew);

        $this->assertSame($userNew, $this->contact->getUser());
    }

    public function testName(): void
    {
        $this->assertSame(self::NAME, $this->contact->getName());

        $newName = 'New Name';
        $this->contact->setName($newName);
        $this->assertSame($newName, $this->contact->getName());
    }

    public function testEmail(): void
    {
        $this->assertSame(self::EMAIL, $this->contact->getEmail());

        $newEmail = 'testNew@gmail.com';
        $this->contact->setEmail($newEmail);
        $this->assertSame($newEmail, $this->contact->getEmail());
    }

    public function testPhoneNumber(): void
    {
        $phoneNumber = $this->contact->getPhoneNumber();
        $phoneNumberString = $this->phoneNumberUtil->format($phoneNumber, PhoneNumberFormat::INTERNATIONAL);
        $this->assertSame(self::PHONE_NUMBER, $phoneNumberString);

        $newExpectedPhoneNumber = '+420 607 555 666';
        $newPhoneNumber = $this->phoneNumberUtil->parse($newExpectedPhoneNumber);
        $this->contact->setPhoneNumber($newPhoneNumber);
        $phoneNumber = $this->contact->getPhoneNumber();
        $phoneNumberString = $this->phoneNumberUtil->format($phoneNumber, PhoneNumberFormat::INTERNATIONAL);
        $this->assertSame($newExpectedPhoneNumber, $phoneNumberString);
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        /** @var PhoneNumberUtil $phoneNumberUtil */
        $phoneNumberUtil = $container->get(PhoneNumberUtil::class);
        $this->phoneNumberUtil = $phoneNumberUtil;

        $phoneNumber = $this->phoneNumberUtil->parse(self::PHONE_NUMBER);

        $this->user = new User('user@test.com');
        $this->contact = new Contact(self::NAME, self::EMAIL, $phoneNumber, $this->user);
    }
}