<?php

namespace App\Security\Hasher;

use App\Entity\UserRegistration;

/**
 * Hashes and verifies user registration verifiers.
 */
interface UserRegistrationVerifierHasherInterface
{
    /**
     * Hashes the given plain-text verifier.
     *
     * @param string $plainVerifier
     * @return string
     */
    public function hashVerifier(string $plainVerifier): string;

    /**
     * Checks if the plain-text verifier matches the hashed verifier.
     *
     * @param UserRegistration $userRegistration
     * @param string $plainVerifier
     * @return bool
     */
    public function isVerifierValid(UserRegistration $userRegistration, string $plainVerifier): bool;
}