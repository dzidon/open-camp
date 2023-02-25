<?php

namespace App\DependencyInjection\Compiler;

use App\Menu\Registry\MenuTypeRegistryInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * A compiler pass which makes the menu type registry work. It looks for all services tagged as "app.menu_factory" and
 * adds them to the registry.
 */
class MenuTypeRegistryPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(MenuTypeRegistryInterface::class))
        {
            return;
        }

        $definition = $container->findDefinition(MenuTypeRegistryInterface::class);
        $taggedServices = $container->findTaggedServiceIds('app.menu_factory');

        foreach ($taggedServices as $id => $tags)
        {
            $definition->addMethodCall('registerFactory', [new Reference($id)]);
        }
    }
}