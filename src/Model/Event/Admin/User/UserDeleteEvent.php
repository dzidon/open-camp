<?php

namespace App\Model\Event\Admin\User;

use App\Model\Entity\User;
use App\Model\Event\AbstractModelEvent;

class UserDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.user.delete';

    private User $entity;

    public function __construct(User $entity)
    {
        $this->entity = $entity;
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