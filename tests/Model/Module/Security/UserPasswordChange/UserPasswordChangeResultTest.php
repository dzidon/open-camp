<?php

namespace App\Tests\Model\Module\Security\UserPasswordChange;

use App\Model\Entity\UserPasswordChange;
use App\Model\Module\Security\UserPasswordChange\UserPasswordChangeResult;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Tests the user password change result.
 */
class UserPasswordChangeResultTest extends TestCase
{
    public function testResult(): void
    {
        $passwordChange = new UserPasswordChange(new DateTimeImmutable('now'), 'abc', '123');
        $result = new UserPasswordChangeResult($passwordChange, 'xyz', true);

        $this->assertTrue($result->isFake());
        $this->assertSame($passwordChange, $result->getUserPasswordChange());
        $this->assertSame('xyz', $result->getPlainVerifier());
        $this->assertSame('abcxyz', $result->getToken());
    }
}