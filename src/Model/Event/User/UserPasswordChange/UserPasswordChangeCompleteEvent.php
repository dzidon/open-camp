<?php

namespace App\Model\Event\User\UserPasswordChange;

use App\Library\Data\User\PlainPasswordData;
use App\Model\Entity\UserPasswordChange;
use Symfony\Contracts\EventDispatcher\Event;

class UserPasswordChangeCompleteEvent extends Event
{
    public const NAME = 'model.user.user_password_change.complete';

    private PlainPasswordData $data;

    private UserPasswordChange $entity;

    public function __construct(PlainPasswordData $data, UserPasswordChange $entity)
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

    public function getUserPasswordChange(): UserPasswordChange
    {
        return $this->entity;
    }

    public function setUserPasswordChange(UserPasswordChange $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}