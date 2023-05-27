<?php

namespace App\Tests\DependencyInjection\Compiler;

use App\DependencyInjection\Compiler\MenuTypeFactoryRegistryPass;
use App\Menu\Registry\MenuTypeFactoryRegistry;
use App\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\Menu\Factory\MenuTypeFactoryMock;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Tests the menu type factory registry compiler pass.
 */
class MenuTypeRegistryPassTest extends KernelTestCase
{
    /**
     * Tests that the compiler pass properly registers menu factories tagged with 'app.menu_factory'.
     *
     * @return void
     * @throws Exception
     */
    public function testMenuTypeRegistryCompilerPass(): void
    {
        $container = new ContainerBuilder();
        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);

        $container
            ->register(MenuTypeFactoryMock::class, MenuTypeFactoryMock::class)
            ->addTag('app.menu_factory')
            ->setPublic(true)
        ;

        $registry = $this->getMenuTypeFactoryRegistry();
        $container
            ->register(MenuTypeFactoryRegistryInterface::class, $registry::class)
            ->setPublic(true)
        ;

        $pass = new MenuTypeFactoryRegistryPass();
        $pass->process($container);
        $container->compile();

        /** @var MenuTypeFactoryRegistry $registry */
        $registry = $container->get(MenuTypeFactoryRegistryInterface::class);
        $this->assertNotNull($registry);

        $menuType = $registry->buildMenuType('menu_mock');
        $this->assertNotNull($menuType);
        $this->assertSame('menu_mock', $menuType->getIdentifier());
    }

    /**
     * Returns an instance of the menu type factory registry from the service container.
     *
     * @return MenuTypeFactoryRegistryInterface
     * @throws Exception
     */
    private function getMenuTypeFactoryRegistry(): MenuTypeFactoryRegistryInterface
    {
        $container = static::getContainer();

        /** @var MenuTypeFactoryRegistryInterface $registry */
        $registry = $container->get(MenuTypeFactoryRegistryInterface::class);

        return $registry;
    }
}