<?php

namespace App\Security\Hasher;

use App\Model\Entity\UserPasswordChange;

/**
 * Hashes and verifies user password change verifiers.
 */
interface UserPasswordChangeVerifierHasherInterface
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
     * @param UserPasswordChange $userPasswordChange
     * @param string $plainVerifier
     * @return bool
     */
    public function isVerifierValid(UserPasswordChange $userPasswordChange, string $plainVerifier): bool;
}