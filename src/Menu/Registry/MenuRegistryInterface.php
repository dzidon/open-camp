<?php

namespace App\Menu\Registry;

use App\Menu\Factory\MenuTypeFactoryInterface;
use App\Menu\Type\MenuTypeInterface;

/**
 * A central menu registry works as a cache for all menus used in the app.
 */
interface MenuRegistryInterface
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
     * Removes a specific record (factory & menu pair) from the registry.
     *
     * @param string $identifier
     * @return $this
     */
    public function removeRecord(string $identifier): self;

    /**
     * Returns an instance of the specified menu. If forceRebuild is set to false, the same instance will be
     * returned every time. If forceRebuild is set to true, the method instantiates the menu again using its factory
     * (if found), saves it in the registry and returns it.
     *
     * @param string $identifier
     * @param bool $forceRebuild
     * @return MenuTypeInterface|null
     */
    public function getMenuType(string $identifier, bool $forceRebuild = false): ?MenuTypeInterface;
}