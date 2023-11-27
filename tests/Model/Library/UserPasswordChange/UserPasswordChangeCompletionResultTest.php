<?php

namespace App\Tests\Model\Library\UserPasswordChange;

use App\Model\Entity\UserPasswordChange;
use App\Model\Library\UserPasswordChange\UserPasswordChangeCompletionResult;
use DateTimeImmutable;
use LogicException;
use PHPUnit\Framework\TestCase;
use stdClass;

class UserPasswordChangeCompletionResultTest extends TestCase
{
    private UserPasswordChange $usedUserPasswordChange;

    private array $disabledUserPasswordChanges;

    public function testResult(): void
    {
        $result = new UserPasswordChangeCompletionResult($this->usedUserPasswordChange, $this->disabledUserPasswordChanges);

        $this->assertSame($this->usedUserPasswordChange, $result->getUsedUserPasswordChange());
        $this->assertSame($this->disabledUserPasswordChanges, $result->getDisabledUserPasswordChanges());
    }

    public function testEmptyResult(): void
    {
        $result = new UserPasswordChangeCompletionResult();

        $this->assertNull($result->getUsedUserPasswordChange());
        $this->assertEmpty($result->getDisabledUserPasswordChanges());
    }

    public function testResultWithInvalidDisabledUserPasswordChanges(): void
    {
        $this->expectException(LogicException::class);
        new UserPasswordChangeCompletionResult($this->usedUserPasswordChange, [new stdClass()]);
    }

    protected function setUp(): void
    {
        $this->usedUserPasswordChange = new UserPasswordChange(new DateTimeImmutable(), 'xyz', '123');

        $this->disabledUserPasswordChanges = [
            new UserPasswordChange(new DateTimeImmutable(), 'foo', '123'),
            new UserPasswordChange(new DateTimeImmutable(), 'bar', '123'),
        ];
    }
}