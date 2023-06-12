<?php

namespace App\Tests\Entity;

use App\Entity\UserRegistration;
use App\Enum\Entity\UserRegistrationStateEnum;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * Tests the UserRegistration entity.
 */
class UserRegistrationTest extends TestCase
{
    private const EMAIL = 'abc@gmail.com';
    private const EXPIRE_AT = '2023-06-10 12:00:00';
    private const SELECTOR = 'abc';
    private const VERIFIER = 'xyz';

    private UserRegistration $userRegistration;

    /**
     * Tests the email getter and setter.
     *
     * @return void
     */
    public function testEmail(): void
    {
        $this->assertSame(self::EMAIL, $this->userRegistration->getEmail());

        $newEmail = 'xyz@gmail.com';
        $this->userRegistration->setEmail($newEmail);
        $this->assertSame($newEmail, $this->userRegistration->getEmail());
    }

    /**
     * Tests the expiration date getter and setter.
     *
     * @return void
     * @throws Exception
     */
    public function testExpireAt(): void
    {
        $expireAt = $this->userRegistration->getExpireAt();
        $this->assertSame(self::EXPIRE_AT, $expireAt->format('Y-m-d H:i:s'));

        $newExpireAtString = '2024-07-11 13:00:00';
        $newExpireAt = new DateTimeImmutable($newExpireAtString);
        $this->userRegistration->setExpireAt($newExpireAt);
        $expireAt = $this->userRegistration->getExpireAt();
        $this->assertSame($newExpireAtString, $expireAt->format('Y-m-d H:i:s'));
    }

    /**
     * Tests the state getter and setter.
     *
     * @return void
     */
    public function testState(): void
    {
        $this->assertSame(UserRegistrationStateEnum::UNUSED->value, $this->userRegistration->getState());

        $this->userRegistration->setState(UserRegistrationStateEnum::USED);
        $this->assertSame(UserRegistrationStateEnum::USED->value, $this->userRegistration->getState());

        $this->userRegistration->setState(UserRegistrationStateEnum::DISABLED);
        $this->assertSame(UserRegistrationStateEnum::DISABLED->value, $this->userRegistration->getState());
    }

    /**
     * Tests the selector getter and setter.
     *
     * @return void
     */
    public function testSelector(): void
    {
        $this->assertSame(self::SELECTOR, $this->userRegistration->getSelector());

        $newSelector = 'foo';
        $this->userRegistration->setSelector($newSelector);
        $this->assertSame($newSelector, $this->userRegistration->getSelector());
    }

    /**
     * Tests the verifier getter and setter.
     *
     * @return void
     */
    public function testVerifier(): void
    {
        $this->assertSame(self::VERIFIER, $this->userRegistration->getVerifier());

        $newVerifier = 'foo';
        $this->userRegistration->setVerifier($newVerifier);
        $this->assertSame($newVerifier, $this->userRegistration->getVerifier());
    }

    protected function setUp(): void
    {
        $expireAt = new DateTimeImmutable(self::EXPIRE_AT);
        $this->userRegistration = new UserRegistration(self::EMAIL, $expireAt, self::SELECTOR, self::VERIFIER);
    }
}