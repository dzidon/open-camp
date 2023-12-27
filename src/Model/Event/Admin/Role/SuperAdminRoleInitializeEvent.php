<?php

namespace App\Model\Event\Admin\Role;

use App\Model\Entity\Role;
use App\Model\Event\AbstractModelEvent;

class SuperAdminRoleInitializeEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.role.super_admin_initialize';

    private ?Role $role = null;

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }
}