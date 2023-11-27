<?php

namespace App\Tests\Model\Library\UserRegistration;

use App\Model\Entity\User;
use App\Model\Entity\UserRegistration;
use App\Model\Library\UserRegistration\UserRegistrationCompletionResult;
use DateTimeImmutable;
use LogicException;
use PHPUnit\Framework\TestCase;
use stdClass;

class UserRegistrationCompletionResultTest extends TestCase
{
    private User $user;

    private UserRegistration $usedUserRegistration;

    private array $disabledUserRegistrations;

    public function testResult(): void
    {
        $result = new UserRegistrationCompletionResult($this->user, $this->usedUserRegistration, $this->disabledUserRegistrations);

        $this->assertSame($this->user, $result->getUser());
        $this->assertSame($this->usedUserRegistration, $result->getUsedUserRegistration());
        $this->assertSame($this->disabledUserRegistrations, $result->getDisabledUserRegistrations());
    }

    public function testEmptyResult(): void
    {
        $result = new UserRegistrationCompletionResult();

        $this->assertNull($result->getUser());
        $this->assertNull($result->getUsedUserRegistration());
        $this->assertEmpty($result->getDisabledUserRegistrations());
    }

    public function testResultWithInvalidDisabledUserRegistrations(): void
    {
        $this->expectException(LogicException::class);
        new UserRegistrationCompletionResult($this->user, $this->usedUserRegistration, [new stdClass()]);
    }

    protected function setUp(): void
    {
        $this->user = new User('bob@gmail.com');
        $this->usedUserRegistration = new UserRegistration('bob@gmail.com', new DateTimeImmutable(), 'abc', '123');
        $this->disabledUserRegistrations = [
            new UserRegistration('bob@gmail.com', new DateTimeImmutable(), 'foo', '123'),
            new UserRegistration('bob@gmail.com', new DateTimeImmutable(), 'bar', '123'),
        ];
    }
}