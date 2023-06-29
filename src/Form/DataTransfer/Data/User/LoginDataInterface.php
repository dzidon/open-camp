<?php

namespace App\Form\DataTransfer\Data\User;

/**
 * User login data.
 */
interface LoginDataInterface
{
    public function getEmail(): string;

    public function setEmail(?string $email): self;

    public function getPassword(): string;

    public function setPassword(?string $password): self;

    public function isRememberMe(): bool;

    public function setRememberMe(bool $rememberMe): self;
}