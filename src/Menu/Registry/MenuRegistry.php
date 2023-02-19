<?php

namespace App\Menu\Registry;

use App\Menu\Factory\MenuTypeFactoryInterface;
use App\Menu\Type\MenuTypeInterface;

/**
 * @inheritDoc
 */
class MenuRegistry implements MenuRegistryInterface
{
    /**
     * @var MenuRegistryRecord[]
     */
    private array $records = [];

    /**
     * @inheritDoc
     */
    public function registerFactory(MenuTypeFactoryInterface $factory): self
    {
        $identifier = $factory::getMenuIdentifier();
        $record = $this->findExistingRecordOrCreateNew($identifier);
        $record->setFactory($factory);
        $this->records[$identifier] = $record;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function registerMenuType(MenuTypeInterface $menuType): MenuRegistryInterface
    {
        $identifier = $menuType->getIdentifier();
        $record = $this->findExistingRecordOrCreateNew($identifier);
        $record->setMenuType($menuType);
        $this->records[$identifier] = $record;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeRecord(string $identifier): self
    {
        if (array_key_exists($identifier, $this->records))
        {
            unset($this->records[$identifier]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMenuType(string $identifier, bool $forceRebuild = false): ?MenuTypeInterface
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

        if ($menuType === null || $forceRebuild)
        {
            $newMenuType = $factory->buildMenuType();
            $record->setMenuType($newMenuType);
        }

        return $record->getMenuType();
    }

    /**
     * Helper method for finding a record in the registry. If no record with the specified identifier is found,
     * a new record is returned.
     *
     * @param string $identifier
     * @return MenuRegistryRecord
     */
    private function findExistingRecordOrCreateNew(string $identifier): MenuRegistryRecord
    {
        if (array_key_exists($identifier, $this->records))
        {
            return $this->records[$identifier];
        }
        else
        {
            return new MenuRegistryRecord();
        }
    }
}