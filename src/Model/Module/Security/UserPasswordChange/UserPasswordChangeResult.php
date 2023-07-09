<?php

namespace App\Model\Module\Security\UserPasswordChange;

use App\Model\Entity\UserPasswordChange;

/**
 * @inheritDoc
 */
class UserPasswordChangeResult implements UserPasswordChangeResultInterface
{
    private UserPasswordChange $userPasswordChange;
    private string $plainVerifier;
    private string $token;
    private bool $fake;

    public function __construct(UserPasswordChange $userPasswordChange, string $plainVerifier, bool $fake)
    {
        $this->userPasswordChange = $userPasswordChange;
        $this->plainVerifier = $plainVerifier;
        $this->fake = $fake;

        $this->token = sprintf('%s%s', $this->userPasswordChange->getSelector(), $this->plainVerifier);
    }

    /**
     * @inheritDoc
     */
    public function getUserPasswordChange(): UserPasswordChange
    {
        return $this->userPasswordChange;
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