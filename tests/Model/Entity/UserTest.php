<?php

namespace App\Tests\Model\Entity;

use App\Model\Entity\Permission;
use App\Model\Entity\PermissionGroup;
use App\Model\Entity\Role;
use App\Model\Entity\User;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

class UserTest extends TestCase
{
    private const EMAIL = 'abc@gmail.com';

    private User $user;

    public function testId(): void
    {
        $id = $this->user->getId();
        $this->assertInstanceOf(UuidV4::class, $id);
    }

    public function testEmail(): void
    {
        $this->assertSame(self::EMAIL, $this->user->getEmail());

        $newEmail = 'xyz@gmail.com';
        $this->user->setEmail($newEmail);
        $this->assertSame($newEmail, $this->user->getEmail());
    }

    public function testUserIdentifier(): void
    {
        $this->assertSame(self::EMAIL, $this->user->getUserIdentifier());
    }

    public function testRoles(): void
    {
        $this->assertSame(['ROLE_USER'], $this->user->getRoles());

        $group = new PermissionGroup('group', 'Group', 0);
        $perm1 = new Permission('perm1', 'Permission 1', 0, $group);
        $perm2 = new Permission('perm2', 'Permission 2', 0, $group);
        $role = new Role('Role');
        $role
            ->addPermission($perm1)
            ->addPermission($perm2)
        ;

        $this->user->setRole($role);
        $this->assertSame(['ROLE_USER', 'perm1', 'perm2'], $this->user->getRoles());
    }

    public function testPassword(): void
    {
        $this->assertNull($this->user->getPassword());

        $this->user->setPassword('xyz');
        $this->assertSame('xyz', $this->user->getPassword());

        $this->user->setPassword(null);
        $this->assertNull($this->user->getPassword());
    }

    public function testHasPermission(): void
    {
        $this->assertFalse($this->user->hasPermission('perm1'));

        $group = new PermissionGroup('group', 'Group', 0);
        $perm1 = new Permission('perm1', 'Permission 1', 0, $group);
        $role = new Role('Role');
        $role->addPermission($perm1);

        $this->user->setRole($role);
        $this->assertTrue($this->user->hasPermission('perm1'));
    }

    public function testRole(): void
    {
        $this->assertNull($this->user->getRole());

        $role = new Role('Role');
        $this->user->setRole($role);
        $this->assertSame($role, $this->user->getRole());

        $roleNew = new Role('Role new');
        $this->user->setRole($roleNew);
        $this->assertSame($roleNew, $this->user->getRole());

        $this->user->setRole(null);
        $this->assertNull($this->user->getRole());
    }

    public function testNameFirst(): void
    {
        $this->assertNull($this->user->getNameFirst());

        $this->user->setNameFirst('text');
        $this->assertSame('text', $this->user->getNameFirst());

        $this->user->setNameFirst(null);
        $this->assertNull($this->user->getNameFirst());
    }

    public function testNameLast(): void
    {
        $this->assertNull($this->user->getNameLast());

        $this->user->setNameLast('text');
        $this->assertSame('text', $this->user->getNameLast());

        $this->user->setNameLast(null);
        $this->assertNull($this->user->getNameLast());
    }

    public function testStreet(): void
    {
        $this->assertNull($this->user->getStreet());

        $this->user->setStreet('text');
        $this->assertSame('text', $this->user->getStreet());

        $this->user->setStreet(null);
        $this->assertNull($this->user->getStreet());
    }

    public function testTown(): void
    {
        $this->assertNull($this->user->getTown());

        $this->user->setTown('text');
        $this->assertSame('text', $this->user->getTown());

        $this->user->setTown(null);
        $this->assertNull($this->user->getTown());
    }

    public function testZip(): void
    {
        $this->assertNull($this->user->getZip());

        $this->user->setZip('text');
        $this->assertSame('text', $this->user->getZip());

        $this->user->setZip(null);
        $this->assertNull($this->user->getZip());
    }

    public function testCountry(): void
    {
        $this->assertNull($this->user->getCountry());

        $this->user->setCountry('text');
        $this->assertSame('text', $this->user->getCountry());

        $this->user->setCountry(null);
        $this->assertNull($this->user->getCountry());
    }

    public function testBusinessName(): void
    {
        $this->assertNull($this->user->getBusinessName());

        $this->user->setBusinessName('text');
        $this->assertSame('text', $this->user->getBusinessName());

        $this->user->setBusinessName(null);
        $this->assertNull($this->user->getBusinessName());
    }

    public function testBusinessCin(): void
    {
        $this->assertNull($this->user->getBusinessCin());

        $this->user->setBusinessCin('text');
        $this->assertSame('text', $this->user->getBusinessCin());

        $this->user->setBusinessCin(null);
        $this->assertNull($this->user->getBusinessCin());
    }

    public function testBusinessVatId(): void
    {
        $this->assertNull($this->user->getBusinessVatId());

        $this->user->setBusinessVatId('text');
        $this->assertSame('text', $this->user->getBusinessVatId());

        $this->user->setBusinessVatId(null);
        $this->assertNull($this->user->getBusinessVatId());
    }

    public function testLastActiveAt(): void
    {
        $this->assertNull($this->user->getLastActiveAt());

        $newLastActiveAtString = '2024-07-11 13:00:00';
        $newLastActiveAt = new DateTimeImmutable($newLastActiveAtString);
        $this->user->setLastActiveAt($newLastActiveAt);
        $lastActiveAt = $this->user->getLastActiveAt();
        $this->assertSame($newLastActiveAtString, $lastActiveAt->format('Y-m-d H:i:s'));
    }

    public function testCreatedAt(): void
    {
        $this->assertSame((new DateTimeImmutable('now'))->getTimestamp(), $this->user->getCreatedAt()->getTimestamp());
    }

    public function testUpdatedAt(): void
    {
        $this->assertNull($this->user->getUpdatedAt());
    }

    protected function setUp(): void
    {
        $this->user = new User(self::EMAIL);
    }
}