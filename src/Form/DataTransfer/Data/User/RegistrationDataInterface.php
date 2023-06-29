<?php

namespace App\Form\DataTransfer\Data\User;

/**
 * User registration data.
 */
interface RegistrationDataInterface
{
    public function getEmail(): string;

    public function setEmail(?string $email): self;

    public function getCaptcha(): ?string;

    public function setCaptcha(?string $captcha): void;

    public function isPrivacy(): bool;

    public function setPrivacy(bool $privacy): self;

    public function isTerms(): bool;

    public function setTerms(bool $terms): self;
}