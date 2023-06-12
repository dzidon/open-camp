<?php

namespace App\Tests\Security;

use App\Entity\UserRegistration;
use App\Security\UserRegistrationCreationResult;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Tests UserRegistrationCreationResult.
 */
class UserRegistrationCreationResultTest extends TestCase
{
    public function testResult(): void
    {
        $registration = new UserRegistration('a@b.com', new DateTimeImmutable('now'), 'abc', 'xyz');
        $result = new UserRegistrationCreationResult($registration, true, 'plain_verifier');

        $this->assertTrue($result->isFake());
        $this->assertSame($registration, $result->getUserRegistration());
        $this->assertSame('plain_verifier', $result->getPlainVerifier());
    }
}