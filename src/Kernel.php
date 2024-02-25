<?php

namespace App;

use App\Library\DependencyInjection\Compiler\BreadcrumbsRegistryPass;
use App\Library\DependencyInjection\Compiler\DataTransferRegistryPass;
use App\Library\DependencyInjection\Compiler\MenuTypeFactoryRegistryPass;
use App\Library\DependencyInjection\Compiler\OfflinePaymentInstructionRendererRegistryPass;
use App\Library\DependencyInjection\Compiler\PublicServicesInTestCompilerPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        $container
            ->addCompilerPass(new MenuTypeFactoryRegistryPass())
            ->addCompilerPass(new DataTransferRegistryPass())
            ->addCompilerPass(new PublicServicesInTestCompilerPass())
            ->addCompilerPass(new BreadcrumbsRegistryPass())
            ->addCompilerPass(new OfflinePaymentInstructionRendererRegistryPass())
        ;
    }
}
