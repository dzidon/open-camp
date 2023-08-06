<?php

namespace App\Tests\Service\Security\Hasher;

use App\Model\Entity\UserPasswordChange;
use App\Service\Security\Hasher\UserPasswordChangeVerifierHasher;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests UserPasswordChange verifier hashing.
 */
class UserPasswordChangeVerifierHasherTest extends KernelTestCase
{
    public function testHasher(): void
    {
        $hasher = $this->getHasher();
        $plainVerifier = '369258147';

        $expireAt = new DateTimeImmutable('now');
        $verifier = $hasher->hashVerifier($plainVerifier);
        $passwordChange = new UserPasswordChange($expireAt, 'abc', $verifier);

        $this->assertTrue($hasher->isVerifierValid($passwordChange, $plainVerifier));
    }

    private function getHasher(): UserPasswordChangeVerifierHasher
    {
        $container = static::getContainer();

        /** @var UserPasswordChangeVerifierHasher $hasher */
        $hasher = $container->get(UserPasswordChangeVerifierHasher::class);

        return $hasher;
    }
}