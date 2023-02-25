<?php

namespace App\Menu\Renderer;

use App\Menu\Type\MenuTypeInterface;

/**
 * Turns menu types into HTML.
 */
interface MenuTypeRendererInterface
{
    /**
     * Renders an instance of a menu type (returns its HTML).
     *
     * @param MenuTypeInterface $menuType
     * @return string
     */
    public function renderMenuType(MenuTypeInterface $menuType): string;
}