<?php

namespace App\Menu\Breadcrumbs\User;

use App\Controller\User\ProfileController;
use App\Menu\Type\MenuTypeInterface;

/**
 * Creates breadcrumbs for {@link ProfileController}.
 */
interface ProfileBreadcrumbsInterface
{
    /**
     * Initializes breadcrumbs for the path "user_password_change".
     */
    public function buildPasswordChange(): MenuTypeInterface;

    /**
     * Initializes breadcrumbs for the path "user_profile_password_change_create".
     */
    public function buildPasswordChangeCreate(): MenuTypeInterface;
}