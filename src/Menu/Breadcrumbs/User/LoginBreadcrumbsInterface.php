<?php

namespace App\Menu\Breadcrumbs\User;

/**
 * Adds login page breadcrumbs to the central menu registry.
 */
interface LoginBreadcrumbsInterface
{
    /**
     * Initializes breadcrumbs for the path "user_login".
     */
    public function addLoginToMenuRegistry(): void;
}