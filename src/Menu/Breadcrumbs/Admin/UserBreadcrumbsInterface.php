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
     *
     * @return MenuTypeInterface
     */
    public function buildList(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_user_create".
     *
     * @return MenuTypeInterface
     */
    public function buildCreate(): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_user_read".
     *
     * @param int $userId
     * @return MenuTypeInterface
     */
    public function buildRead(int $userId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_user_update".
     *
     * @param int $userId
     * @return MenuTypeInterface
     */
    public function buildUpdate(int $userId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_password_update".
     *
     * @param int $userId
     * @return MenuTypeInterface
     */
    public function buildUpdatePassword(int $userId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_user_delete".
     *
     * @param int $userId
     * @return MenuTypeInterface
     */
    public function buildDelete(int $userId): MenuTypeInterface;
}