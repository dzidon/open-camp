<?php

namespace App\Model\Event\Admin\Permission;

use Symfony\Contracts\EventDispatcher\Event;

class PermissionsAndGroupsCreateEvent extends Event
{
    public const NAME = 'model.admin.permissions_and_groups.create';
}