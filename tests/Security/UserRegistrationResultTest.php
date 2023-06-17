<?php

namespace App\Tests\Security;

use App\Entity\UserRegistration;
use App\Security\UserRegistrationResult;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

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