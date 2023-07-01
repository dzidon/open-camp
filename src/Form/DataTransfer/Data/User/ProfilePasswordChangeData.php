<?php

namespace App\Form\DataTransfer\Data\User;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * See {@link ProfilePasswordChangeType}
 */
class ProfilePasswordChangeData implements ProfilePasswordChangeDataInterface
{
    private string $currentPassword = '';

    #[Assert\Valid]
    private PlainPasswordData $newPasswordData;

    public function __construct()
    {
        $this->newPasswordData = new PlainPasswordData();
    }

    public function getCurrentPassword(): string
    {
        return $this->currentPassword;
    }

    public function setCurrentPassword(?string $currentPassword): self
    {
        $this->currentPassword = (string) $currentPassword;

        return $this;
    }

    public function getNewPasswordData(): PlainPasswordData
    {
        return $this->newPasswordData;
    }
}