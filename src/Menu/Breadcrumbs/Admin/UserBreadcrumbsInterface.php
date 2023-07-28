<?php

namespace App\Menu\Breadcrumbs\Admin;

use App\Controller\Admin\UserController;
use App\Menu\Type\MenuTypeInterface;
use Symfony\Component\Uid\UuidV4;

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
    public function buildRead(UuidV4 $userId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_user_update".
     */
    public function buildUpdate(UuidV4 $userId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_password_update".
     */
    public function buildUpdatePassword(UuidV4 $userId): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_user_delete".
     */
    public function buildDelete(UuidV4 $userId): MenuTypeInterface;
}