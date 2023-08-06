<?php

namespace App\Library\Data\User;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @inheritDoc
 */
class ProfilePasswordChangeData implements ProfilePasswordChangeDataInterface
{
    #[SecurityAssert\UserPassword]
    private ?string $currentPassword = null;

    #[Assert\Valid]
    private PlainPasswordData $newPasswordData;

    public function __construct()
    {
        $this->newPasswordData = new PlainPasswordData();
    }

    public function getCurrentPassword(): ?string
    {
        return $this->currentPassword;
    }

    public function setCurrentPassword(?string $currentPassword): self
    {
        $this->currentPassword = $currentPassword;

        return $this;
    }

    public function getNewPasswordData(): PlainPasswordData
    {
        return $this->newPasswordData;
    }
}