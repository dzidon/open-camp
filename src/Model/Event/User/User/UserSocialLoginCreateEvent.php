<?php

namespace App\Model\Event\User\User;

use App\Model\Entity\User;
use App\Model\Event\AbstractModelEvent;

class UserSocialLoginCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.user.social_login_create';

    private string $email;

    private ?User $entity;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->entity;
    }

    public function setUser(?User $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}