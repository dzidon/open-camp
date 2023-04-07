<?php

namespace App\Menu\Breadcrumbs\Admin;

/**
 * Adds admin profile breadcrumbs to the central menu registry.
 */
interface ProfileBreadcrumbsInterface
{
    /**
     * Initializes breadcrumbs for path "admin_profile".
     *
     * @return void
     */
    public function initializeIndex(): void;
}