<?php

namespace App\Menu\Breadcrumbs\Admin;

use App\Controller\Admin\UserController;
use App\Menu\Type\MenuTypeInterface;

/**
 * Creates breadcrumbs for {@link UserController}.
 */
interface UserBreadcrumbsInterface
{
    /**
     * Creates breadcrumbs for the path "admin_user_list".
     */
    public function buildList(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_user_create".
     */
    public function buildCreate(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_user_read".
     */
    public function buildRead(int $userId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_user_update".
     */
    public function buildUpdate(int $userId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_password_update".
     */
    public function buildUpdatePassword(int $userId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_user_delete".
     */
    public function buildDelete(int $userId): MenuTypeInterface;
}