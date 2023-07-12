<?php

namespace App\Form\DataTransfer\Data\User;

/**
 * @inheritDoc
 */
class LoginData implements LoginDataInterface
{
    private ?string $email = null;

    private ?string $password = null;

    private bool $rememberMe = false;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

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