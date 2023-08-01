<?php

namespace App\Tests\Model\Entity;

use App\Enum\Entity\ContactRoleEnum;
use App\Model\Entity\Contact;
use App\Model\Entity\User;
use DateTimeImmutable;
use libphonenumber\PhoneNumber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

class ContactTest extends TestCase
{
    private const NAME_FIRST = 'John';
    private const NAME_LAST = 'Doe';
    private const ROLE = ContactRoleEnum::MOTHER;

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

    public function testNameFirst(): void
    {
        $this->assertSame(self::NAME_FIRST, $this->contact->getNameFirst());

        $newName = 'Johny';
        $this->contact->setNameFirst($newName);
        $this->assertSame($newName, $this->contact->getNameFirst());
    }

    public function testNameLast(): void
    {
        $this->assertSame(self::NAME_LAST, $this->contact->getNameLast());

        $newName = 'Last';
        $this->contact->setNameLast($newName);
        $this->assertSame($newName, $this->contact->getNameLast());
    }

    public function testEmail(): void
    {
        $this->assertNull($this->contact->getEmail());

        $newEmail = 'testNew@gmail.com';
        $this->contact->setEmail($newEmail);
        $this->assertSame($newEmail, $this->contact->getEmail());

        $this->contact->setEmail(null);
        $this->assertNull($this->contact->getEmail());
    }

    public function testPhoneNumber(): void
    {
        $this->assertNull($this->contact->getEmail());

        $phoneNumber = new PhoneNumber();
        $phoneNumber->setCountryCode(421);
        $phoneNumber->setNationalNumber('605222333');

        $this->contact->setPhoneNumber($phoneNumber);

        $this->assertNotSame($phoneNumber, $this->contact->getPhoneNumber());
        $this->assertSame(421, $this->contact->getPhoneNumber()->getCountryCode());
        $this->assertSame('605222333', $this->contact->getPhoneNumber()->getNationalNumber());

        $this->contact->setPhoneNumber(null);
        $this->assertNull($this->contact->getPhoneNumber());
    }

    public function testGender(): void
    {
        $this->assertSame(self::ROLE, $this->contact->getRole());

        $newRole = ContactRoleEnum::MOTHER;
        $this->contact->setRole($newRole);
        $this->assertSame($newRole, $this->contact->getRole());
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
        $this->user = new User('user@test.com');
        $this->contact = new Contact(self::NAME_FIRST, self::NAME_LAST, self::ROLE, $this->user);
    }
}