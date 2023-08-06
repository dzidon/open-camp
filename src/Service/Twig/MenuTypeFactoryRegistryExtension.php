<?php

namespace App\Service\Twig;

use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Adds menu creation functionality to Twig.
 */
class MenuTypeFactoryRegistryExtension extends AbstractExtension
{
    private MenuTypeFactoryRegistryInterface $factoryRegistry;

    public function __construct(MenuTypeFactoryRegistryInterface $factoryRegistry)
    {
        $this->factoryRegistry = $factoryRegistry;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('build_menu', [$this->factoryRegistry, 'buildMenuType']),
        ];
    }
}