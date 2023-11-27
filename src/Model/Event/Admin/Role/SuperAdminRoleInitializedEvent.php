<?php

namespace App\Model\Event\Admin\Role;

use App\Model\Entity\Role;
use Symfony\Contracts\EventDispatcher\Event;

class SuperAdminRoleInitializedEvent extends Event
{
    public const NAME = 'model.admin.role.super_admin_initialized';

    private Role $role;

    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function setRole(Role $role): self
    {
        $this->role = $role;

        return $this;
    }
}