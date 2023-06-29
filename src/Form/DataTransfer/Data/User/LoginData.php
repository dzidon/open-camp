<?php

namespace App\Form\DataTransfer\Data\User;

use App\Form\Type\User\LoginType;

/**
 * See {@link LoginType}
 */
class LoginData implements LoginDataInterface
{
    private string $email = '';

    private string $password = '';

    private bool $rememberMe = false;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = (string) $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = (string) $password;

        return $this;
    }

    public function isRememberMe(): bool
    {
        return $this->rememberMe;
    }

    public function setRememberMe(bool $rememberMe): self
    {
        $this->rememberMe = $rememberMe;

        return $this;
    }
}