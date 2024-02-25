<?php

namespace App\Library\DependencyInjection\Compiler;

use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MenuTypeFactoryRegistryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(MenuTypeFactoryRegistryInterface::class))
        {
            return;
        }

        $definition = $container->findDefinition(MenuTypeFactoryRegistryInterface::class);
        $taggedServices = $container->findTaggedServiceIds('app.menu_factory');

        foreach ($taggedServices as $id => $tags)
        {
            $definition->addMethodCall('registerFactory', [new Reference($id)]);
        }
    }
}