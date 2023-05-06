<?php

namespace App\Menu\Registry;

use App\Menu\Factory\MenuTypeFactoryInterface;
use App\Menu\Type\MenuTypeInterface;

/**
 * @inheritDoc
 */
class MenuTypeRegistry implements MenuTypeRegistryInterface
{
    /**
     * @var MenuTypeRegistryRecord[]
     */
    private array $records = [];

    /**
     * @inheritDoc
     */
    public function registerFactory(MenuTypeFactoryInterface $factory): self
    {
        $record = new MenuTypeRegistryRecord();
        $record->setFactory($factory);

        $identifier = $factory::getMenuIdentifier();
        $this->records[$identifier] = $record;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function registerMenuType(MenuTypeInterface $menuType): MenuTypeRegistryInterface
    {
        $record = new MenuTypeRegistryRecord();
        $record->setMenuType($menuType);

        $identifier = $menuType->getIdentifier();
        $this->records[$identifier] = $record;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMenuType(string $identifier): ?MenuTypeInterface
    {
        if (!array_key_exists($identifier, $this->records))
        {
            return null;
        }

        $record = $this->records[$identifier];
        $factory = $record->getFactory();
        $menuType = $record->getMenuType();

        if ($factory === null)
        {
            return $menuType;
        }

        if ($menuType === null)
        {
            $newMenuType = $factory->buildMenuType();
            $record->setMenuType($newMenuType);
        }

        return $record->getMenuType();
    }
}