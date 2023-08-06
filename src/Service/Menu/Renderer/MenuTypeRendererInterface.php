<?php

namespace App\Service\Menu\Renderer;

use App\Library\Menu\MenuTypeInterface;

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