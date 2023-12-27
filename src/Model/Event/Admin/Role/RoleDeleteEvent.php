<?php

namespace App\Model\Event\Admin\Role;

use App\Model\Entity\Role;
use App\Model\Event\AbstractModelEvent;

class RoleDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.role.delete';

    private Role $entity;

    public function __construct(Role $entity)
    {
        $this->entity = $entity;
    }

    public function getRole(): Role
    {
        return $this->entity;
    }

    public function setRole(Role $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}