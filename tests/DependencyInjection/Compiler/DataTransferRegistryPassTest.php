<?php

namespace App\Tests\DependencyInjection\Compiler;

use App\DependencyInjection\Compiler\DataTransferRegistryPass;
use App\Form\DataTransfer\Registry\DataTransferRegistry;
use App\Form\DataTransfer\Registry\DataTransferRegistryInterface;
use App\Tests\Form\DataTransfer\Transfer\NameDataTransferMock;
use Exception;
use ReflectionObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Tests the data transfer registry compiler pass.
 */
class DataTransferRegistryPassTest extends KernelTestCase
{
    /**
     * Tests that the compiler pass properly registers data transfer services tagged as 'app.data_transfer'.
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
            ->register(NameDataTransferMock::class, NameDataTransferMock::class)
            ->addTag('app.data_transfer')
            ->setPublic(true)
        ;

        $registry = $this->getDataTransferRegistry();
        $container
            ->register(DataTransferRegistryInterface::class, $registry::class)
            ->setPublic(true)
        ;

        $pass = new DataTransferRegistryPass();
        $pass->process($container);
        $container->compile();

        /** @var DataTransferRegistry $registry */
        $registry = $container->get(DataTransferRegistryInterface::class);

        $reflection = new ReflectionObject($registry);
        $property = $reflection->getProperty('dataTransfers');
        $dataTransfers = $property->getValue($registry);

        $this->assertCount(1, $dataTransfers);
        $this->assertSame(NameDataTransferMock::class, $dataTransfers[0]::class);
    }
    
    private function getDataTransferRegistry(): DataTransferRegistry
    {
        $container = static::getContainer();

        /** @var DataTransferRegistry $registry */
        $registry = $container->get(DataTransferRegistry::class);

        return $registry;
    }
}