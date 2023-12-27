<?php

namespace App\Model\Event\Admin\Role;

use App\Library\Data\Admin\RoleData;
use App\Model\Entity\Role;
use App\Model\Event\AbstractModelEvent;

class RoleUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.role.update';

    private RoleData $data;

    private Role $entity;

    public function __construct(RoleData $data, Role $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getRoleData(): RoleData
    {
        return $this->data;
    }

    public function setRoleData(RoleData $data): self
    {
        $this->data = $data;

        return $this;
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