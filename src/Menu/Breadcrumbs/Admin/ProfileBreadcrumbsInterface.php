<?php

namespace App\Menu\Breadcrumbs\Admin;

use App\Menu\Type\MenuTypeInterface;

/**
 * Adds admin profile breadcrumbs to the central menu registry.
 */
interface ProfileBreadcrumbsInterface
{
    /**
     * Initializes breadcrumbs for the path "admin_profile".
     *
     * @return MenuTypeInterface
     */
    public function initializeProfile(): MenuTypeInterface;
}