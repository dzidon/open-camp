<?php

namespace App\Model\Library\UserRegistration;

use App\Model\Entity\UserRegistration;

/**
 * @inheritDoc
 */
class UserRegistrationResult implements UserRegistrationResultInterface
{
    private UserRegistration $userRegistration;
    private string $plainVerifier;
    private string $token;
    private bool $fake;

    public function __construct(UserRegistration $userRegistration, string $plainVerifier, bool $fake)
    {
        $this->userRegistration = $userRegistration;
        $this->plainVerifier = $plainVerifier;
        $this->fake = $fake;

        $this->token = sprintf('%s%s', $this->userRegistration->getSelector(), $this->plainVerifier);
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
        return $this->token;
    }

    /**
     * @inheritDoc
     */
    public function isFake(): bool
    {
        return $this->fake;
    }
}