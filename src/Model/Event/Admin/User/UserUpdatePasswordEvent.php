<?php

namespace App\Model\Event\Admin\User;

use App\Library\Data\Admin\PlainPasswordData;
use App\Model\Entity\User;
use App\Model\Event\AbstractModelEvent;

class UserUpdatePasswordEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.user.update_password';

    private PlainPasswordData $data;

    private User $entity;

    public function __construct(PlainPasswordData $data, User $entity)
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