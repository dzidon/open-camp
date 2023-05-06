<?php

namespace App\Menu\Breadcrumbs\Admin;

/**
 * Adds admin profile breadcrumbs to the central menu registry.
 */
interface ProfileBreadcrumbsInterface
{
    /**
     * Initializes breadcrumbs for the path "admin_profile".
     */
    public function addProfileToMenuRegistry(): void;
}