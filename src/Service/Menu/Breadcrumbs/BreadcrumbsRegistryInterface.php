<?php

namespace App\Service\Menu\Breadcrumbs;

use App\Library\Menu\MenuTypeInterface;

/**
 * Creates breadcrumbs.
 */
interface BreadcrumbsRegistryInterface
{
    /**
     * Registers a class that creates a breadcrumb link.
     *
     * @param BreadcrumbInterface $breadcrumb
     * @return void
     * @internal
     *
     */
    public function registerBreadcrumb(BreadcrumbInterface $breadcrumb): void;

    /**
     * Returns a breadcrumbs menu for the given route.
     *
     * @param string $route
     * @param array $options
     * @param string $block Template block of the breadcrumbs' menu.
     * @return MenuTypeInterface
     */
    public function getBreadcrumbs(string $route, array $options = [], string $block = 'breadcrumbs'): MenuTypeInterface;
}