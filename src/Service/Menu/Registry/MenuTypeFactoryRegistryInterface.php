<?php

namespace App\Service\Menu\Registry;

use App\Library\Menu\MenuTypeInterface;
use App\Service\Menu\Factory\MenuTypeFactoryInterface;

/**
 * A central menu factory registry.
 */
interface MenuTypeFactoryRegistryInterface
{
    /**
     * Registers a menu factory for later menu instantiation.
     *
     * @internal
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