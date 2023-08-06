<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Controller\Admin\RoleController;
use App\Library\Menu\MenuTypeInterface;
use Symfony\Component\Uid\UuidV4;

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
    public function buildRead(UuidV4 $roleId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_role_update".
     */
    public function buildUpdate(UuidV4 $roleId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_role_delete".
     */
    public function buildDelete(UuidV4 $roleId): MenuTypeInterface;
}