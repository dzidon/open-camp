<?php

namespace App\Model\Event\Admin\User;

use App\Library\Data\Admin\UserData;
use App\Model\Entity\User;
use App\Model\Event\AbstractModelEvent;

class UserUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.user.update';

    private UserData $data;

    private User $entity;

    public function __construct(UserData $data, User $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getUserData(): UserData
    {
        return $this->data;
    }

    public function setUserData(UserData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getUser(): User
    {
        return $this->entity;
    }

    public function setUser(User $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}