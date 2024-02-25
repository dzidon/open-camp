<?php

namespace App\Library\DependencyInjection\Compiler;

use App\Model\Service\PaymentMethod\OfflineInstructions\OfflineInstructionRendererRegistryInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class OfflinePaymentInstructionRendererRegistryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(OfflineInstructionRendererRegistryInterface::class))
        {
            return;
        }

        $definition = $container->findDefinition(OfflineInstructionRendererRegistryInterface::class);
        $taggedServices = $container->findTaggedServiceIds('app.offline_payment_instruction_renderer');

        foreach ($taggedServices as $id => $tags)
        {
            $definition->addMethodCall('registerOfflineInstructionRenderer', [new Reference($id)]);
        }
    }
}