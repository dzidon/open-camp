<?php

namespace App\Tests\Menu\Registry;

use App\Menu\Registry\MenuTypeRegistryRecord;
use App\Menu\Type\MenuType;
use App\Tests\Menu\Factory\MenuTypeFactoryMock;
use PHPUnit\Framework\TestCase;

/**
 * Tests one record used in a central menu registry.
 */
class MenuTypeRegistryRecordTest extends TestCase
{
    /**
     * Tests factory getter and setter.
     *
     * @return void
     */
    public function testFactory(): void
    {
        $menuRegistryRecord = $this->createMenuTypeRegistryRecord();
        $factory = $menuRegistryRecord->getFactory();
        $this->assertNull($factory);

        $menuRegistryRecord->setFactory(new MenuTypeFactoryMock());
        $factory = $menuRegistryRecord->getFactory();
        $this->assertNotNull($factory);
        $this->assertSame('menu_mock', $factory::getMenuIdentifier());

        $menuRegistryRecord->setFactory(null);
        $factory = $menuRegistryRecord->getFactory();
        $this->assertNull($factory);
    }

    /**
     * Tests menu type getter and setter.
     *
     * @return void
     */
    public function testMenuType(): void
    {
        $menuRegistryRecord = $this->createMenuTypeRegistryRecord();
        $menuType = $menuRegistryRecord->getMenuType();
        $this->assertNull($menuType);

        $menuRegistryRecord->setMenuType(new MenuType('menu', 'menu_block'));
        $menuType = $menuRegistryRecord->getMenuType();
        $this->assertNotNull($menuType);
        $this->assertSame('menu', $menuType->getIdentifier());

        $menuRegistryRecord->setMenuType(null);
        $menuType = $menuRegistryRecord->getMenuType();
        $this->assertNull($menuType);
    }

    /**
     * Creates and returns a menu registry record.
     *
     * @return MenuTypeRegistryRecord
     */
    private function createMenuTypeRegistryRecord(): MenuTypeRegistryRecord
    {
        return new MenuTypeRegistryRecord();
    }
}