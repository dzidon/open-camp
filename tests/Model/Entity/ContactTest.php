<?php

namespace App\Tests\Model\Entity;

use App\Model\Entity\Contact;
use App\Model\Entity\User;
use App\Model\Enum\Entity\ContactRoleEnum;
use DateTimeImmutable;
use libphonenumber\PhoneNumber;
use LogicException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

class ContactTest extends TestCase
{
    private const NAME_FIRST = 'John';
    private const NAME_LAST = 'Doe';
    private const ROLE = ContactRoleEnum::OTHER;
    private const ROLE_OTHER = 'Role other';

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
        $this->assertNull($this->contact->getPhoneNumber());

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

    public function testRole(): void
    {
        $this->assertSame(self::ROLE, $this->contact->getRole());
        $this->assertSame(self::ROLE_OTHER, $this->contact->getRoleOther());

        $newRoleOther = 'New role other';
        $this->contact->setRoleOther($newRoleOther);
        $this->assertSame($newRoleOther, $this->contact->getRoleOther());

        $newRole = ContactRoleEnum::MOTHER;
        $this->contact->setRole($newRole, self::ROLE_OTHER);
        $this->assertSame($newRole, $this->contact->getRole());
        $this->assertNull($this->contact->getRoleOther());

        $this->contact->setRoleOther(self::ROLE_OTHER);
        $this->assertNull($this->contact->getRoleOther());

        $this->contact->setRole(self::ROLE, self::ROLE_OTHER);
        $this->assertSame(self::ROLE, $this->contact->getRole());
        $this->assertSame(self::ROLE_OTHER, $this->contact->getRoleOther());

        $this->contact->setRoleOther($newRoleOther);
        $this->assertSame($newRoleOther, $this->contact->getRoleOther());
    }

    public function testRoleOtherNullInConstructor(): void
    {
        $this->expectException(LogicException::class);
        $this->contact = new Contact(self::NAME_FIRST, self::NAME_LAST, $this->user, ContactRoleEnum::OTHER, null);
    }

    public function testRoleOtherNullInSetter(): void
    {
        $this->expectException(LogicException::class);
        $this->contact->setRoleOther(null);
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
        $this->contact = new Contact(self::NAME_FIRST, self::NAME_LAST, $this->user, self::ROLE, self::ROLE_OTHER);
    }
}