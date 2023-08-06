<?php

namespace App\Service\Menu\Breadcrumbs\User;

use App\Controller\User\PasswordChangeController;
use App\Library\Menu\MenuTypeInterface;

/**
 * Creates breadcrumbs for {@link PasswordChangeController}.
 */
interface PasswordChangeBreadcrumbsInterface
{
    /**
     * Initializes breadcrumbs for the path "user_password_change".
     */
    public function buildPasswordChange(): MenuTypeInterface;

    /**
     * Initializes breadcrumbs for the path "user_password_change_complete".
     */
    public function buildPasswordChangeComplete(string $token): MenuTypeInterface;
}