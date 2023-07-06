<?php

namespace App\Tests\Entity;

use App\Entity\Permission;
use App\Entity\PermissionGroup;
use App\Entity\Role;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private const EMAIL = 'abc@gmail.com';

    private User $user;

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
        $this->assertSame($this->user->getRole(), $role);

        $roleNew = new Role('Role new');
        $this->user->setRole($roleNew);
        $this->assertSame($this->user->getRole(), $roleNew);

        $this->user->setRole(null);
        $this->assertNull($this->user->getRole());
    }

    public function testName(): void
    {
        $this->assertNull($this->user->getName());

        $this->user->setName('text');
        $this->assertSame('text', $this->user->getName());

        $this->user->setName(null);
        $this->assertNull($this->user->getName());
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

    protected function setUp(): void
    {
        $this->user = new User(self::EMAIL);
    }
}