<?php

namespace App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Makes all services in the test env public. Without this, services that are not type-hinted in the app would
 * be unavailable in tests.
 */
class PublicServicesInTestCompilerPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$this->isPHPUnit())
        {
            return;
        }

        foreach ($container->getAliases() as $definition)
        {
            $definition->setPublic(true);
        }
    }

    /**
     * Returns true if used in a test.
     *
     * @return bool
     */
    private function isPHPUnit(): bool
    {
        return defined('PHPUNIT_COMPOSER_INSTALL') || defined('__PHPUNIT_PHAR__');
    }
}