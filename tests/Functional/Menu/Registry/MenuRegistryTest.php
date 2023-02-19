<?php

namespace App\Tests\Functional\Menu\Registry;

use App\Menu\Registry\MenuRegistry;
use App\Menu\Type\MenuType;
use App\Tests\Functional\Menu\Factory\MenuTypeFactoryMock;
use PHPUnit\Framework\TestCase;

/**
 * Tests the central menu registry.
 */
class MenuRegistryTest extends TestCase
{
    /**
     * Tests that a factory properly instantiates its menu type and that the menu registry caches it.
     *
     * @return void
     */
    public function testGetMenuTypeUsingFactory(): void
    {
        $menuRegistry = $this->createMenuRegistry();
        $menuType = $menuRegistry->getMenuType('menu_mock');
        $this->assertNotNull($menuType);
        $this->assertSame('menu_mock', $menuType->getIdentifier());

        $this->assertSame(null, $menuType->getText());
        $menuType->setText('xyz');
        $menuRegistry->registerFactory(new MenuTypeFactoryMock());
        $menuType = $menuRegistry->getMenuType('menu_mock');
        $this->assertSame('xyz', $menuType->getText());

        $menuRegistry->registerMenuType($menuType);
        $menuType = $menuRegistry->getMenuType('menu_mock', true);
        $this->assertNotNull($menuType);
        $this->assertSame('menu_mock', $menuType->getIdentifier());
        $this->assertSame(null, $menuType->getText());
    }

    /**
     * Tests that menu types can be stored & retrieved manually.
     *
     * @return void
     */
    public function testGetMenuTypeUsingMenuType(): void
    {
        // create the menu registry, check that it contains the manually added menu
        $menuRegistry = $this->createMenuRegistry();
        $menuType = $menuRegistry->getMenuType('manually_added_menu');
        $this->assertNotNull($menuType);
        $this->assertSame('manually_added_menu', $menuType->getIdentifier());

        // check if the menu registry returns menus with updated values
        $this->assertSame(null, $menuType->getText());
        $menuType->setText('xyz');
        $menuType = $menuRegistry->getMenuType('manually_added_menu');
        $this->assertSame('xyz', $menuType->getText());

        // "forceRebuild" does not matter if it's a manually added menu without a factory
        $menuType = $menuRegistry->getMenuType('manually_added_menu', true);
        $this->assertNotNull($menuType);
        $this->assertSame('manually_added_menu', $menuType->getIdentifier());
        $this->assertSame('xyz', $menuType->getText());
    }

    /**
     * Tests that a record (factory & menu type pair) can be removed from the registry.
     *
     * @return void
     */
    public function testRemoveRecord(): void
    {
        $menuRegistry = $this->createMenuRegistry();
        $menuType = $menuRegistry->getMenuType('manually_added_menu');
        $this->assertNotNull($menuType);

        $menuRegistry->removeRecord('manually_added_menu');
        $menuType = $menuRegistry->getMenuType('manually_added_menu');
        $this->assertNull($menuType);
    }

    /**
     * Creates and returns a menu registry.
     *
     * @return MenuRegistry
     */
    private function createMenuRegistry(): MenuRegistry
    {
        $menuRegistry = new MenuRegistry();
        $menuRegistry->registerFactory(new MenuTypeFactoryMock());
        $menuRegistry->registerMenuType(new MenuType('manually_added_menu', 'menu_block'));
        return $menuRegistry;
    }
}