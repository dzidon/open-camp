<?php

namespace App\Tests\Model\Entity;

use App\Enum\Entity\UserPasswordChangeStateEnum;
use App\Model\Entity\User;
use App\Model\Entity\UserPasswordChange;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UserPasswordChangeTest extends TestCase
{
    private const EXPIRE_AT = '2023-06-10 12:00:00';
    private const SELECTOR = 'abc';
    private const VERIFIER = 'xyz';

    private UserPasswordChange $userPasswordChange;

    public function testUser(): void
    {
        $this->assertNull($this->userPasswordChange->getUser());

        $user = new User('bob@gmail.com');
        $this->userPasswordChange->setUser($user);
        $this->assertSame($user, $this->userPasswordChange->getUser());

        $userNew = new User('bob-new@bing.com');
        $this->userPasswordChange->setUser($userNew);
        $this->assertSame($userNew, $this->userPasswordChange->getUser());

        $this->userPasswordChange->setUser(null);
        $this->assertNull($this->userPasswordChange->getUser());
    }

    public function testExpireAt(): void
    {
        $expireAt = $this->userPasswordChange->getExpireAt();
        $this->assertSame(self::EXPIRE_AT, $expireAt->format('Y-m-d H:i:s'));

        $newExpireAtString = '2024-07-11 13:00:00';
        $newExpireAt = new DateTimeImmutable($newExpireAtString);
        $this->userPasswordChange->setExpireAt($newExpireAt);
        $expireAt = $this->userPasswordChange->getExpireAt();
        $this->assertSame($newExpireAtString, $expireAt->format('Y-m-d H:i:s'));
    }

    public function testState(): void
    {
        $this->assertSame(UserPasswordChangeStateEnum::UNUSED->value, $this->userPasswordChange->getState());

        $this->userPasswordChange->setState(UserPasswordChangeStateEnum::USED);
        $this->assertSame(UserPasswordChangeStateEnum::USED->value, $this->userPasswordChange->getState());

        $this->userPasswordChange->setState(UserPasswordChangeStateEnum::DISABLED);
        $this->assertSame(UserPasswordChangeStateEnum::DISABLED->value, $this->userPasswordChange->getState());
    }

    public function testSelector(): void
    {
        $this->assertSame(self::SELECTOR, $this->userPasswordChange->getSelector());

        $newSelector = 'foo';
        $this->userPasswordChange->setSelector($newSelector);
        $this->assertSame($newSelector, $this->userPasswordChange->getSelector());
    }

    public function testVerifier(): void
    {
        $this->assertSame(self::VERIFIER, $this->userPasswordChange->getVerifier());

        $newVerifier = 'foo';
        $this->userPasswordChange->setVerifier($newVerifier);
        $this->assertSame($newVerifier, $this->userPasswordChange->getVerifier());
    }

    public function testIsActive(): void
    {
        $future = new DateTimeImmutable('3000-01-01 12:00:00');
        $past = new DateTimeImmutable('2000-01-01 12:00:00');
        $user = new User('bob@gmail.com');

        // active
        $this->userPasswordChange->setUser($user);
        $this->userPasswordChange->setExpireAt($future);
        $this->userPasswordChange->setState(UserPasswordChangeStateEnum::UNUSED);
        $this->assertTrue($this->userPasswordChange->isActive());

        // inactive - user is null
        $this->userPasswordChange->setUser(null);
        $this->userPasswordChange->setExpireAt($future);
        $this->userPasswordChange->setState(UserPasswordChangeStateEnum::UNUSED);
        $this->assertFalse($this->userPasswordChange->isActive());

        // inactive - expiration date in the past
        $this->userPasswordChange->setUser($user);
        $this->userPasswordChange->setExpireAt($past);
        $this->userPasswordChange->setState(UserPasswordChangeStateEnum::UNUSED);
        $this->assertFalse($this->userPasswordChange->isActive());

        // inactive - state is disabled
        $this->userPasswordChange->setUser($user);
        $this->userPasswordChange->setExpireAt($future);
        $this->userPasswordChange->setState(UserPasswordChangeStateEnum::DISABLED);
        $this->assertFalse($this->userPasswordChange->isActive());

        // inactive - state is used
        $this->userPasswordChange->setUser($user);
        $this->userPasswordChange->setExpireAt($future);
        $this->userPasswordChange->setState(UserPasswordChangeStateEnum::USED);
        $this->assertFalse($this->userPasswordChange->isActive());
    }

    protected function setUp(): void
    {
        $expireAt = new DateTimeImmutable(self::EXPIRE_AT);
        $this->userPasswordChange = new UserPasswordChange($expireAt, self::SELECTOR, self::VERIFIER);
    }
}