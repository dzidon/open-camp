<?php

namespace App\Menu\Registry;

use App\Menu\Factory\MenuTypeFactoryInterface;
use App\Menu\Type\MenuTypeInterface;

/**
 * Data structure used by a menu registry. A central menu registry uses records that consist of factory & menu pairs.
 */
interface MenuTypeRegistryRecordInterface
{
    /**
     * Returns the factory used for later menu creation.
     *
     * @return MenuTypeFactoryInterface|null
     */
    public function getFactory(): ?MenuTypeFactoryInterface;

    /**
     * Sets the factory used for later menu creation.
     *
     * @param MenuTypeFactoryInterface|null $factory
     * @return $this
     */
    public function setFactory(?MenuTypeFactoryInterface $factory): self;

    /**
     * Returns the cached menu.
     *
     * @return MenuTypeInterface|null
     */
    public function getMenuType(): ?MenuTypeInterface;

    /**
     * Sets the cached menu.
     *
     * @param MenuTypeInterface|null $menuType
     * @return $this
     */
    public function setMenuType(?MenuTypeInterface $menuType): self;
}