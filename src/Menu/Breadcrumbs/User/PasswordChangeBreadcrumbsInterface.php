<?php

namespace App\Menu\Breadcrumbs\User;

use App\Controller\User\PasswordChangeController;
use App\Menu\Type\MenuTypeInterface;

/**
 * Creates breadcrumbs for {@link PasswordChangeController}.
 */
interface PasswordChangeBreadcrumbsInterface
{
    /**
     * Initializes breadcrumbs for the path "user_password_change".
     *
     * @return MenuTypeInterface
     */
    public function buildPasswordChange(): MenuTypeInterface;

    /**
     * Initializes breadcrumbs for the path "user_password_change_complete".
     *
     * @param string $token
     * @return MenuTypeInterface
     */
    public function buildPasswordChangeComplete(string $token): MenuTypeInterface;
}