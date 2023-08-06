<?php

namespace App\Tests\Service\Menu\Registry;

use App\Service\Menu\Registry\MenuTypeFactoryRegistry;
use App\Tests\Service\Menu\Factory\MenuTypeFactoryMock;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests the central menu type factory registry.
 */
class MenuTypeFactoryRegistryTest extends KernelTestCase
{
    /**
     * Tests that a factory properly instantiates its menu type.
     *
     * @return void
     */
    public function testBuildMenuType(): void
    {
        $menuRegistry = $this->getMenuTypeFactoryRegistry();
        $menuType = $menuRegistry->buildMenuType('menu_mock', ['button_text' => 'Click here...']);
        $this->assertNotNull($menuType);
        $this->assertSame('menu_mock', $menuType->getIdentifier());

        $button = $menuType->getChild('button');
        $this->assertSame('Click here...', $button->getText());
    }

    private function getMenuTypeFactoryRegistry(): MenuTypeFactoryRegistry
    {
        $container = static::getContainer();

        /** @var MenuTypeFactoryRegistry $menuRegistry */
        $menuRegistry = $container->get(MenuTypeFactoryRegistry::class);
        $menuRegistry->registerFactory(new MenuTypeFactoryMock());

        return $menuRegistry;
    }
}