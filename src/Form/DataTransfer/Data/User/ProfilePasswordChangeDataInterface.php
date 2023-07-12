<?php

namespace App\Form\DataTransfer\Data\User;

/**
 * Profile password change data.
 */
interface ProfilePasswordChangeDataInterface
{
    public function getCurrentPassword(): ?string;

    public function setCurrentPassword(?string $currentPassword): self;

    public function getNewPasswordData(): PlainPasswordDataInterface;
}