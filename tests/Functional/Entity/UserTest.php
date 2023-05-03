<?php

namespace App\Tests\Functional\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Tests the User entity.
 */
class UserTest extends TestCase
{
    private const EMAIL = 'abc@gmail.com';

    private User $user;

    /**
     * Tests the e-mail getter and setter.
     *
     * @return void
     */
    public function testEmail(): void
    {
        $this->assertSame(self::EMAIL, $this->user->getEmail());

        $newEmail = 'xyz@gmail.com';
        $this->user->setEmail($newEmail);
        $this->assertSame($newEmail, $this->user->getEmail());
    }

    /**
     * Tests the internal user identifier getter.
     *
     * @return void
     */
    public function testUserIdentifier(): void
    {
        $this->assertSame(self::EMAIL, $this->user->getUserIdentifier());
    }

    /**
     * Tests the internal roles getter.
     *
     * @return void
     */
    public function testRoles(): void
    {
        $this->assertSame(['ROLE_USER'], $this->user->getRoles());
    }

    /**
     * Tests the password getter and setter.
     *
     * @return void
     */
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