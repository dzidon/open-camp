<?php

namespace App\Service\Menu\Breadcrumbs\User;

use App\Controller\User\ProfileController;
use App\Library\Menu\MenuTypeInterface;

/**
 * Creates breadcrumbs for {@link ProfileController}.
 */
interface ProfileBreadcrumbsInterface
{
    /**
     * Initializes breadcrumbs for the path "user_profile_billing".
     */
    public function buildBilling(): MenuTypeInterface;

    /**
     * Initializes breadcrumbs for the path "user_profile_password_change".
     */
    public function buildPasswordChange(): MenuTypeInterface;

    /**
     * Initializes breadcrumbs for the path "user_profile_password_change_create".
     */
    public function buildPasswordChangeCreate(): MenuTypeInterface;
}