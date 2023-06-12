<?php

namespace App\Security;

use App\Entity\UserRegistration;

/**
 * @inheritDoc
 */
class UserRegistrationCreationResult implements UserRegistrationCreationResultInterface
{
    private UserRegistration $userRegistration;
    private bool $fake;
    private string $plainVerifier;

    public function __construct(UserRegistration $userRegistration, bool $fake, string $plainVerifier)
    {
        $this->userRegistration = $userRegistration;
        $this->fake = $fake;
        $this->plainVerifier = $plainVerifier;
    }

    /**
     * @inheritDoc
     */
    public function getUserRegistration(): UserRegistration
    {
        return $this->userRegistration;
    }

    /**
     * @inheritDoc
     */
    public function isFake(): bool
    {
        return $this->fake;
    }

    /**
     * @inheritDoc
     */
    public function getPlainVerifier(): string
    {
        return $this->plainVerifier;
    }
}