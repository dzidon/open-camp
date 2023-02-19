<?php

namespace App\Menu\Registry;

use App\Menu\Factory\MenuTypeFactoryInterface;
use App\Menu\Type\MenuTypeInterface;

/**
 * @inheritDoc
 */
class MenuRegistryRecord implements MenuRegistryRecordInterface
{
    private ?MenuTypeFactoryInterface $factory = null;
    private ?MenuTypeInterface $menuType = null;

    /**
     * @inheritDoc
     */
    public function getFactory(): ?MenuTypeFactoryInterface
    {
        return $this->factory;
    }

    /**
     * @inheritDoc
     */
    public function setFactory(?MenuTypeFactoryInterface $factory): self
    {
        $this->factory = $factory;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMenuType(): ?MenuTypeInterface
    {
        return $this->menuType;
    }

    /**
     * @inheritDoc
     */
    public function setMenuType(?MenuTypeInterface $menuType): self
    {
        $this->menuType = $menuType;
        return $this;
    }
}