<?php

namespace App\Tests\Model\Library\UserRegistration;

use App\Model\Entity\UserRegistration;
use App\Model\Library\UserRegistration\UserRegistrationResult;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Tests the user registration result.
 */
class UserRegistrationResultTest extends TestCase
{
    public function testResult(): void
    {
        $registration = new UserRegistration('a@b.com', new DateTimeImmutable('now'), 'abc', '123');
        $result = new UserRegistrationResult($registration, 'xyz', true);

        $this->assertTrue($result->isFake());
        $this->assertSame($registration, $result->getUserRegistration());
        $this->assertSame('xyz', $result->getPlainVerifier());
        $this->assertSame('abcxyz', $result->getToken());
    }
}