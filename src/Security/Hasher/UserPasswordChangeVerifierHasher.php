<?php

namespace App\Security\Hasher;

use App\Model\Entity\UserPasswordChange;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

/**
 * @inheritDoc
 */
class UserPasswordChangeVerifierHasher implements UserPasswordChangeVerifierHasherInterface
{
    private PasswordHasherFactoryInterface $passwordHasherFactory;

    public function __construct(PasswordHasherFactoryInterface $passwordHasherFactory)
    {
        $this->passwordHasherFactory = $passwordHasherFactory;
    }

    /**
     * @inheritDoc
     */
    public function hashVerifier(string $plainVerifier): string
    {
        return $this->getHasher()->hash($plainVerifier);
    }

    /**
     * @inheritDoc
     */
    public function isVerifierValid(UserPasswordChange $userPasswordChange, string $plainVerifier): bool
    {
        $hashedVerifier = $userPasswordChange->getVerifier();

        return $this->getHasher()->verify($hashedVerifier, $plainVerifier);
    }

    /**
     * Returns the hasher used for user password changes.
     *
     * @return PasswordHasherInterface
     */
    private function getHasher(): PasswordHasherInterface
    {
        return $this->passwordHasherFactory->getPasswordHasher(UserPasswordChange::class);
    }
}