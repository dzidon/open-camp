<?php

namespace App\Tests\Functional\DependencyInjection\Compiler;

use App\DependencyInjection\Compiler\MenuRegistryPass;
use App\Menu\Registry\MenuRegistry;
use App\Menu\Registry\MenuRegistryInterface;
use App\Tests\Functional\Menu\Factory\MenuTypeFactoryMock;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Tests the menu registry compiler pass.
 */
class MenuRegistryPassTest extends TestCase
{
    /**
     * Tests that the compiler pass properly registers menu factories tagged with 'app.menu_factory'
     * to the central menu registry.
     *
     * @return void
     * @throws Exception
     */
    public function testMenuRegistryCompilerPass(): void
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

        $container
            ->register(MenuRegistryInterface::class, MenuRegistry::class)
            ->setPublic(true)
        ;

        $pass = new MenuRegistryPass();
        $pass->process($container);
        $container->compile();

        /** @var MenuRegistry $registry */
        $registry = $container->get(MenuRegistryInterface::class);
        $this->assertNotNull($registry);

        $menuType = $registry->getMenuType('menu_mock');
        $this->assertNotNull($menuType);
        $this->assertSame('menu_mock', $menuType->getIdentifier());
    }
}