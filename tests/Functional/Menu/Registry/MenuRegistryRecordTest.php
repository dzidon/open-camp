<?php

namespace App\Tests\Functional\Menu\Registry;

use App\Menu\Registry\MenuRegistryRecord;
use App\Menu\Type\MenuType;
use App\Tests\Functional\Menu\Factory\MenuTypeFactoryMock;
use PHPUnit\Framework\TestCase;

/**
 * Tests one record used in a central menu registry.
 */
class MenuRegistryRecordTest extends TestCase
{
    /**
     * Tests factory getter and setter.
     *
     * @return void
     */
    public function testFactory(): void
    {
        $menuRegistryRecord = $this->createMenuRegistryRecord();
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
        $menuRegistryRecord = $this->createMenuRegistryRecord();
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
     * @return MenuRegistryRecord
     */
    private function createMenuRegistryRecord(): MenuRegistryRecord
    {
        return new MenuRegistryRecord();
    }
}