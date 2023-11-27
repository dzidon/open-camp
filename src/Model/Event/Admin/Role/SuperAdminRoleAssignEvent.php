<?php

namespace App\Model\Event\Admin\Role;

use App\Model\Entity\Role;
use App\Model\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class SuperAdminRoleAssignEvent extends Event
{
    public const NAME = 'model.admin.role.super_admin_assign';

    private User $user;

    private Role $role;

    public function __construct(User $user, Role $role)
    {
        $this->user = $user;
        $this->role = $role;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $entity): self
    {
        $this->user = $entity;

        return $this;
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