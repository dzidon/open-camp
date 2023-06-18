<?php

namespace App\Security\Hasher;

use App\Entity\UserRegistration;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

/**
 * @inheritDoc
 */
class UserRegistrationVerifierHasher implements UserRegistrationVerifierHasherInterface
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
    public function isVerifierValid(UserRegistration $userRegistration, string $plainVerifier): bool
    {
        $hashedVerifier = $userRegistration->getVerifier();

        return $this->getHasher()->verify($hashedVerifier, $plainVerifier);
    }

    /**
     * Returns the hasher used for user registrations.
     *
     * @return PasswordHasherInterface
     */
    private function getHasher(): PasswordHasherInterface
    {
        return $this->passwordHasherFactory->getPasswordHasher(UserRegistration::class);
    }
}