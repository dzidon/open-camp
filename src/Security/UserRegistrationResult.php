<?php

namespace App\Security;

use App\Entity\UserRegistration;

/**
 * @inheritDoc
 */
class UserRegistrationResult implements UserRegistrationResultInterface
{
    private UserRegistration $userRegistration;
    private string $plainVerifier;
    private bool $fake;

    public function __construct(UserRegistration $userRegistration, string $plainVerifier, bool $fake)
    {
        $this->userRegistration = $userRegistration;
        $this->plainVerifier = $plainVerifier;
        $this->fake = $fake;
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
    public function getPlainVerifier(): string
    {
        return $this->plainVerifier;
    }

    /**
     * @inheritDoc
     */
    public function getToken(): string
    {
        return sprintf('%s%s', $this->userRegistration->getSelector(), $this->plainVerifier);
    }

    /**
     * @inheritDoc
     */
    public function isFake(): bool
    {
        return $this->fake;
    }
}