<?php

namespace App\Menu\Breadcrumbs\Admin;

use App\Controller\Admin\RoleController;
use App\Menu\Type\MenuTypeInterface;

/**
 * Creates breadcrumbs for {@link RoleController}.
 */
interface RoleBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "admin_role_list".
     */
    public function buildList(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_role_create".
     */
    public function buildCreate(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_role_read".
     */
    public function buildRead(int $roleId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_role_update".
     */
    public function buildUpdate(int $roleId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_role_delete".
     */
    public function buildDelete(int $roleId): MenuTypeInterface;
}