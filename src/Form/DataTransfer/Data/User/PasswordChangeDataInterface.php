<?php

namespace App\Form\DataTransfer\Data\User;

/**
 * User password change data.
 */
interface PasswordChangeDataInterface
{
    public function getEmail(): string;

    public function setEmail(?string $email): self;

    public function getCaptcha(): ?string;

    public function setCaptcha(?string $captcha): void;
}