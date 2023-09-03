<?php

namespace App\Service\Menu\Breadcrumbs\Admin;

use App\Controller\Admin\UserController;
use App\Library\Menu\MenuTypeInterface;
use App\Model\Entity\User;

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
    public function buildRead(User $user): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_user_update".
     */
    public function buildUpdate(User $user): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_password_update".
     */
    public function buildUpdatePassword(User $user): MenuTypeInterface;

    /**
     * Creates breadcrumbs for the path "admin_user_delete".
     */
    public function buildDelete(User $user): MenuTypeInterface;
}