<?php

namespace App\Tests\Library\DependencyInjection\Compiler;

use App\Library\DependencyInjection\Compiler\MenuTypeFactoryRegistryPass;
use App\Service\Menu\Registry\MenuTypeFactoryRegistry;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Tests\Service\Menu\Factory\MenuTypeFactoryMock;
use Exception;
use ReflectionObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Tests the menu type factory registry compiler pass.
 */
class MenuTypeRegistryPassTest extends KernelTestCase
{
    /**
     * Tests that the compiler pass properly registers menu factories tagged as 'app.menu_factory'.
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

        $reflection = new ReflectionObject($registry);
        $property = $reflection->getProperty('factories');
        $factories = $property->getValue($registry);

        $this->assertCount(1, $factories);
        $this->assertArrayHasKey('menu_mock', $factories);
        $this->assertSame(MenuTypeFactoryMock::class, $factories['menu_mock']::class);
    }
    
    private function getMenuTypeFactoryRegistry(): MenuTypeFactoryRegistry
    {
        $container = static::getContainer();

        /** @var MenuTypeFactoryRegistry $registry */
        $registry = $container->get(MenuTypeFactoryRegistry::class);

        return $registry;
    }
}