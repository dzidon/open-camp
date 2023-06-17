<?php

namespace App\Tests\Entity;

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
    }

    public function testPassword(): void
    {
        $this->assertNull($this->user->getPassword());

        $this->user->setPassword('xyz');
        $this->assertSame('xyz', $this->user->getPassword());

        $this->user->setPassword(null);
        $this->assertNull($this->user->getPassword());
    }

    protected function setUp(): void
    {
        $this->user = new User(self::EMAIL);
    }
}