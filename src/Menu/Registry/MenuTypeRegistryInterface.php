<?php

namespace App\Menu\Registry;

use App\Menu\Factory\MenuTypeFactoryInterface;
use App\Menu\Type\MenuTypeInterface;

/**
 * A central menu registry works as a cache for menus used in the app.
 */
interface MenuTypeRegistryInterface
{
    /**
     * Registers a menu factory for later menu instantiation.
     *
     * @param MenuTypeFactoryInterface $factory
     * @return $this
     */
    public function registerFactory(MenuTypeFactoryInterface $factory): self;

    /**
     * Registers a specific menu.
     *
     * @param MenuTypeInterface $menuType
     * @return $this
     */
    public function registerMenuType(MenuTypeInterface $menuType): self;

    /**
     * Returns an instance of the specified menu.
     *
     * @param string $identifier
     * @return MenuTypeInterface|null
     */
    public function getMenuType(string $identifier): ?MenuTypeInterface;
}