<?php

namespace App\Model\Event\Admin\Role;

use Symfony\Contracts\EventDispatcher\Event;

class SuperAdminRoleInitializeEvent extends Event
{
    public const NAME = 'model.admin.role.super_admin_initialize';
}