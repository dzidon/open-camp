<?php

namespace App\Menu\Factory;

use App\Menu\Type\MenuTypeInterface;

/**
 * Interface for menu factories that should be added to the menu registry.
 */
interface MenuTypeFactoryInterface
{
    /**
     * Returns a unique menu identifier. The menu will be available in the registry
     * under this identifier.
     *
     * @return string
     */
    public static function getMenuIdentifier(): string;

    /**
     * Instantiates a menu.
     *
     * @return MenuTypeInterface
     */
    public function buildMenuType(): MenuTypeInterface;
}