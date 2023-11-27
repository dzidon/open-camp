<?php

namespace App\Model\Event\Admin\Permission;

use App\Model\Library\Permission\PermissionsAndGroupsCreationResultInterface;
use Symfony\Contracts\EventDispatcher\Event;

class PermissionsAndGroupsCreatedEvent extends Event
{
    public const NAME = 'model.admin.permissions_and_groups.created';

    private PermissionsAndGroupsCreationResultInterface $result;

    public function __construct(PermissionsAndGroupsCreationResultInterface $result)
    {
        $this->result = $result;
    }

    public function getPermissionsAndGroupsCreationResult(): PermissionsAndGroupsCreationResultInterface
    {
        return $this->result;
    }

    public function setPermissionsAndGroupsCreationResult(PermissionsAndGroupsCreationResultInterface $result): self
    {
        $this->result = $result;

        return $this;
    }
}