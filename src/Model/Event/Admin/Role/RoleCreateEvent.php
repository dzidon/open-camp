<?php

namespace App\Model\Event\Admin\Role;

use App\Library\Data\Admin\RoleData;
use Symfony\Contracts\EventDispatcher\Event;

class RoleCreateEvent extends Event
{
    public const NAME = 'model.admin.role.create';

    private RoleData $data;

    public function __construct(RoleData $data)
    {
        $this->data = $data;
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
}