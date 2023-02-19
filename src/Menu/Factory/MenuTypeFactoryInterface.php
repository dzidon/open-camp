<?php

namespace App\Menu\Factory;

use App\Menu\Type\MenuTypeInterface;

/**
 * Interface for all classes that build menus.
 */
interface MenuTypeFactoryInterface
{
    /**
     * Returns a unique menu identifier.
     *
     * @return string
     */
    public static function getMenuIdentifier(): string;

    /**
     * Returns a menu.
     *
     * @return MenuTypeInterface
     */
    public function buildMenuType(): MenuTypeInterface;
}