<?php

namespace App\DependencyInjection\Compiler;

use App\Form\DataTransfer\Registry\DataTransferRegistryInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Ensures that services tagged as "app.data_transfer" get added to the central data transfer registry.
 */
class DataTransferRegistryPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(DataTransferRegistryInterface::class))
        {
            return;
        }

        $definition = $container->findDefinition(DataTransferRegistryInterface::class);
        $taggedServices = $container->findTaggedServiceIds('app.data_transfer');

        foreach ($taggedServices as $id => $tags)
        {
            $definition->addMethodCall('registerDataTransfer', [new Reference($id)]);
        }
    }
}