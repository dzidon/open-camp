<?php

namespace App\Tests\Model\Entity;

use App\Model\Entity\Contact;
use App\Model\Entity\User;
use DateTimeImmutable;
use libphonenumber\PhoneNumber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

class ContactTest extends TestCase
{
    private const NAME = 'Test Contact';
    private const EMAIL = 'test@gmail.com';
    private const PHONE_NUMBER_COUNTRY_CODE = 420;
    private const PHONE_NUMBER_NATIONAL_NUMBER = '724888999';

    private PhoneNumber $phoneNumber;
    private Contact $contact;
    private User $user;

    public function testId(): void
    {
        $id = $this->contact->getId();
        $this->assertInstanceOf(UuidV4::class, $id);
    }

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
        $this->assertNotSame($this->phoneNumber, $this->contact->getPhoneNumber());
        $this->assertSame(self::PHONE_NUMBER_COUNTRY_CODE, $this->contact->getPhoneNumber()->getCountryCode());
        $this->assertSame(self::PHONE_NUMBER_NATIONAL_NUMBER, $this->contact->getPhoneNumber()->getNationalNumber());

        $phoneNumber = new PhoneNumber();
        $phoneNumber->setCountryCode(421);
        $phoneNumber->setNationalNumber('605222333');

        $this->contact->setPhoneNumber($phoneNumber);

        $this->assertNotSame($phoneNumber, $this->contact->getPhoneNumber());
        $this->assertSame(421, $this->contact->getPhoneNumber()->getCountryCode());
        $this->assertSame('605222333', $this->contact->getPhoneNumber()->getNationalNumber());
    }

    public function testCreatedAt(): void
    {
        $this->assertSame((new DateTimeImmutable('now'))->getTimestamp(), $this->contact->getCreatedAt()->getTimestamp());
    }

    public function testUpdatedAt(): void
    {
        $this->assertNull($this->contact->getUpdatedAt());
    }

    protected function setUp(): void
    {
        $this->phoneNumber = new PhoneNumber();
        $this->phoneNumber->setCountryCode(self::PHONE_NUMBER_COUNTRY_CODE);
        $this->phoneNumber->setNationalNumber(self::PHONE_NUMBER_NATIONAL_NUMBER);

        $this->user = new User('user@test.com');
        $this->contact = new Contact(self::NAME, self::EMAIL, $this->phoneNumber, $this->user);
    }
}