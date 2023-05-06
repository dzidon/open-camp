<?php

namespace App\Tests\Functional\Menu\Registry;

use App\Menu\Registry\MenuTypeRegistry;
use App\Menu\Type\MenuType;
use App\Tests\Functional\Menu\Factory\MenuTypeFactoryMock;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests the central menu registry.
 */
class MenuTypeRegistryTest extends KernelTestCase
{
    /**
     * Tests that a factory properly instantiates its menu type and that the menu registry caches it.
     *
     * @return void
     * @throws Exception
     */
    public function testGetMenuTypeUsingFactory(): void
    {
        $menuRegistry = $this->getMenuTypeRegistry();
        $menuType = $menuRegistry->getMenuType('menu_mock');
        $this->assertNotNull($menuType);
        $this->assertSame('menu_mock', $menuType->getIdentifier());

        $this->assertSame(null, $menuType->getText());
        $menuType->setText('xyz');
        $menuRegistry->registerFactory(new MenuTypeFactoryMock());
        $menuType = $menuRegistry->getMenuType('menu_mock');
        $this->assertNotSame('xyz', $menuType->getText());
    }

    /**
     * Tests that menu types can be stored & retrieved manually.
     *
     * @return void
     * @throws Exception
     */
    public function testGetMenuTypeUsingMenuType(): void
    {
        $menuRegistry = $this->getMenuTypeRegistry();
        $menuType = $menuRegistry->getMenuType('manually_added_menu');
        $this->assertNotNull($menuType);
        $this->assertSame('manually_added_menu', $menuType->getIdentifier());
    }

    /**
     * Returns an instance of the menu type registry service from the service container.
     *
     * @return MenuTypeRegistry
     * @throws Exception
     */
    private function getMenuTypeRegistry(): MenuTypeRegistry
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var MenuTypeRegistry $menuRegistry */
        $menuRegistry = $container->get(MenuTypeRegistry::class);
        $menuRegistry->registerFactory(new MenuTypeFactoryMock());
        $menuRegistry->registerMenuType(new MenuType('manually_added_menu', 'menu_block'));

        return $menuRegistry;
    }
}