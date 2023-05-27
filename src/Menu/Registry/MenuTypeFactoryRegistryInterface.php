<?php

namespace App\Menu\Registry;

use App\Menu\Factory\MenuTypeFactoryInterface;
use App\Menu\Type\MenuTypeInterface;

/**
 * A central menu factory registry.
 */
interface MenuTypeFactoryRegistryInterface
{
    /**
     * Registers a menu factory for later menu instantiation.
     *
     * @param MenuTypeFactoryInterface $factory
     * @return void
     */
    public function registerFactory(MenuTypeFactoryInterface $factory): void;

    /**
     * Returns an instance of the specified menu.
     *
     * @param string $identifier
     * @param array $options
     * @return MenuTypeInterface|null
     */
    public function buildMenuType(string $identifier, array $options = []): ?MenuTypeInterface;
}