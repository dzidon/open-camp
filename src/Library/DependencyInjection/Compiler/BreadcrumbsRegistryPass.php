<?php

namespace App\Library\DependencyInjection\Compiler;

use App\Service\Menu\Breadcrumbs\BreadcrumbsRegistryInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BreadcrumbsRegistryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(BreadcrumbsRegistryInterface::class))
        {
            return;
        }

        $definition = $container->findDefinition(BreadcrumbsRegistryInterface::class);
        $taggedServices = $container->findTaggedServiceIds('app.breadcrumb');

        foreach ($taggedServices as $id => $tags)
        {
            $definition->addMethodCall('registerBreadcrumb', [new Reference($id)]);
        }
    }
}