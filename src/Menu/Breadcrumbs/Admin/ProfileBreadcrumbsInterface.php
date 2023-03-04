<?php

namespace App\Menu\Breadcrumbs\Admin;

/**
 * Initializes breadcrumbs for the admin profile controller.
 */
interface ProfileBreadcrumbsInterface
{
    /**
     * Adds the admin profile breadcrumbs to the central menu registry.
     *
     * @return void
     */
    public function initializeIndex(): void;
}