<?php

namespace App\Tests\Security\Hasher;

use App\Model\Entity\UserRegistration;
use App\Security\Hasher\UserRegistrationVerifierHasher;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests UserRegistration verifier hashing.
 */
class UserRegistrationVerifierHasherTest extends KernelTestCase
{
    public function testHasher(): void
    {
        $hasher = $this->getHasher();
        $plainVerifier = '369258147';

        $expireAt = new DateTimeImmutable('now');
        $verifier = $hasher->hashVerifier($plainVerifier);
        $registration = new UserRegistration('bob@gmail.com', $expireAt, 'abc', $verifier);

        $this->assertTrue($hasher->isVerifierValid($registration, $plainVerifier));
    }

    private function getHasher(): UserRegistrationVerifierHasher
    {
        $container = static::getContainer();

        /** @var UserRegistrationVerifierHasher $hasher */
        $hasher = $container->get(UserRegistrationVerifierHasher::class);

        return $hasher;
    }
}