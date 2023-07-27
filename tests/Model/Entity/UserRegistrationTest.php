<?php

namespace App\Tests\Model\Entity;

use App\Enum\Entity\UserRegistrationStateEnum;
use App\Model\Entity\UserRegistration;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UserRegistrationTest extends TestCase
{
    private const EMAIL = 'abc@gmail.com';
    private const EXPIRE_AT = '2023-06-10 12:00:00';
    private const SELECTOR = 'abc';
    private const VERIFIER = 'xyz';

    private UserRegistration $userRegistration;

    public function testEmail(): void
    {
        $this->assertSame(self::EMAIL, $this->userRegistration->getEmail());

        $newEmail = 'xyz@gmail.com';
        $this->userRegistration->setEmail($newEmail);
        $this->assertSame($newEmail, $this->userRegistration->getEmail());
    }

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

    public function testState(): void
    {
        $this->assertSame(UserRegistrationStateEnum::UNUSED, $this->userRegistration->getState());

        $this->userRegistration->setState(UserRegistrationStateEnum::USED);
        $this->assertSame(UserRegistrationStateEnum::USED, $this->userRegistration->getState());

        $this->userRegistration->setState(UserRegistrationStateEnum::DISABLED);
        $this->assertSame(UserRegistrationStateEnum::DISABLED, $this->userRegistration->getState());
    }

    public function testSelector(): void
    {
        $this->assertSame(self::SELECTOR, $this->userRegistration->getSelector());

        $newSelector = 'foo';
        $this->userRegistration->setSelector($newSelector);
        $this->assertSame($newSelector, $this->userRegistration->getSelector());
    }

    public function testVerifier(): void
    {
        $this->assertSame(self::VERIFIER, $this->userRegistration->getVerifier());

        $newVerifier = 'foo';
        $this->userRegistration->setVerifier($newVerifier);
        $this->assertSame($newVerifier, $this->userRegistration->getVerifier());
    }

    public function testIsActive(): void
    {
        $future = new DateTimeImmutable('3000-01-01 12:00:00');
        $past = new DateTimeImmutable('2000-01-01 12:00:00');

        // active
        $this->userRegistration->setExpireAt($future);
        $this->userRegistration->setState(UserRegistrationStateEnum::UNUSED);
        $this->assertTrue($this->userRegistration->isActive());

        // inactive - expiration date in the past
        $this->userRegistration->setExpireAt($past);
        $this->userRegistration->setState(UserRegistrationStateEnum::UNUSED);
        $this->assertFalse($this->userRegistration->isActive());

        // inactive - state is disabled
        $this->userRegistration->setExpireAt($future);
        $this->userRegistration->setState(UserRegistrationStateEnum::DISABLED);
        $this->assertFalse($this->userRegistration->isActive());

        // inactive - state is used
        $this->userRegistration->setExpireAt($future);
        $this->userRegistration->setState(UserRegistrationStateEnum::USED);
        $this->assertFalse($this->userRegistration->isActive());
    }

    public function testCreatedAt(): void
    {
        $this->assertSame((new DateTimeImmutable('now'))->getTimestamp(), $this->userRegistration->getCreatedAt()->getTimestamp());
    }

    public function testUpdatedAt(): void
    {
        $this->assertNull($this->userRegistration->getUpdatedAt());
    }

    protected function setUp(): void
    {
        $expireAt = new DateTimeImmutable(self::EXPIRE_AT);
        $this->userRegistration = new UserRegistration(self::EMAIL, $expireAt, self::SELECTOR, self::VERIFIER);
    }
}