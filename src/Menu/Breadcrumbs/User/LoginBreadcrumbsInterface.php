<?php

namespace App\Menu\Breadcrumbs\User;

use App\Menu\Type\MenuTypeInterface;

/**
 * Adds login page breadcrumbs to the central menu registry.
 */
interface LoginBreadcrumbsInterface
{
    /**
     * Initializes breadcrumbs for the path "user_login".
     */
    public function buildLogin(): MenuTypeInterface;
}