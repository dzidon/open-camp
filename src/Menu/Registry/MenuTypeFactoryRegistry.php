<?php

namespace App\Menu\Registry;

use App\Menu\Factory\MenuTypeFactoryInterface;
use App\Menu\Type\MenuTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @inheritDoc
 */
class MenuTypeFactoryRegistry implements MenuTypeFactoryRegistryInterface
{
    /**
     * @var MenuTypeFactoryInterface[]
     */
    private array $factories = [];

    /**
     * @inheritDoc
     */
    public function registerFactory(MenuTypeFactoryInterface $factory): void
    {
        $identifier = $factory::getMenuIdentifier();
        $this->factories[$identifier] = $factory;
    }

    /**
     * @inheritDoc
     */
    public function buildMenuType(string $identifier, array $options = []): ?MenuTypeInterface
    {
        if (!array_key_exists($identifier, $this->factories))
        {
            return null;
        }

        $factory = $this->factories[$identifier];
        $resolver = new OptionsResolver();

        $factory->configureOptions($resolver);
        $resolvedOptions = $resolver->resolve($options);

        return $factory->buildMenuType($resolvedOptions);
    }
}