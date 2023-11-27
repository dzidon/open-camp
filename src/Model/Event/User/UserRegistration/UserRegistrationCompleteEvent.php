<?php

namespace App\Model\Event\User\UserRegistration;

use App\Library\Data\User\PlainPasswordData;
use App\Model\Entity\UserRegistration;
use Symfony\Contracts\EventDispatcher\Event;

class UserRegistrationCompleteEvent extends Event
{
    public const NAME = 'model.user.user_registration.complete';

    private PlainPasswordData $data;

    private UserRegistration $entity;

    public function __construct(PlainPasswordData $data, UserRegistration $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getPlainPasswordData(): PlainPasswordData
    {
        return $this->data;
    }

    public function setPlainPasswordData(PlainPasswordData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getUserRegistration(): UserRegistration
    {
        return $this->entity;
    }

    public function setUserRegistration(UserRegistration $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}