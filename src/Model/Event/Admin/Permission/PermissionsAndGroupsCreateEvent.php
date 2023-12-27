<?php

namespace App\Model\Event\Admin\Permission;

use App\Model\Event\AbstractModelEvent;
use App\Model\Library\Permission\PermissionsAndGroupsCreationResultInterface;

class PermissionsAndGroupsCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.permissions_and_groups.create';

    private ?PermissionsAndGroupsCreationResultInterface $result = null;

    public function getPermissionsAndGroupsCreationResult(): ?PermissionsAndGroupsCreationResultInterface
    {
        return $this->result;
    }

    public function setPermissionsAndGroupsCreationResult(?PermissionsAndGroupsCreationResultInterface $result): self
    {
        $this->result = $result;

        return $this;
    }
}