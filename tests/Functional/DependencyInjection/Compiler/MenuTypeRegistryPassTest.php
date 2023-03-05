<?php

namespace App\Tests\Functional\DependencyInjection\Compiler;

use App\DependencyInjection\Compiler\MenuTypeRegistryPass;
use App\Menu\Registry\MenuTypeRegistry;
use App\Menu\Registry\MenuTypeRegistryInterface;
use App\Tests\Functional\Menu\Factory\MenuTypeFactoryMock;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Tests the menu type registry compiler pass.
 */
class MenuTypeRegistryPassTest extends KernelTestCase
{
    /**
     * Tests that the compiler pass properly registers menu factories tagged with 'app.menu_factory'
     * to the central menu registry.
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

        $registry = $this->getMenuTypeRegistry();
        $container
            ->register(MenuTypeRegistryInterface::class, $registry::class)
            ->setPublic(true)
        ;

        $pass = new MenuTypeRegistryPass();
        $pass->process($container);
        $container->compile();

        /** @var MenuTypeRegistry $registry */
        $registry = $container->get(MenuTypeRegistryInterface::class);
        $this->assertNotNull($registry);

        $menuType = $registry->getMenuType('menu_mock');
        $this->assertNotNull($menuType);
        $this->assertSame('menu_mock', $menuType->getIdentifier());
    }

    /**
     * Returns an instance of the menu type registry from the service container.
     *
     * @return MenuTypeRegistryInterface
     * @throws Exception
     */
    private function getMenuTypeRegistry(): MenuTypeRegistryInterface
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var MenuTypeRegistryInterface $registry */
        $registry = $container->get(MenuTypeRegistryInterface::class);
        return $registry;
    }
}