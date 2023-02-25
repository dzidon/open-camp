<?php

namespace App\Twig;

use App\Menu\Registry\MenuTypeRegistryInterface;
use App\Menu\Renderer\MenuTypeRendererInterface;
use LogicException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Adds menu type rendering functions to Twig.
 */
class MenuTypeRendererExtension extends AbstractExtension
{
    private MenuTypeRendererInterface $menuRenderer;
    private MenuTypeRegistryInterface $menuRegistry;

    public function __construct(MenuTypeRendererInterface $menuRenderer, MenuTypeRegistryInterface $menuRegistry)
    {
        $this->menuRenderer = $menuRenderer;
        $this->menuRegistry = $menuRegistry;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('menu', [$this->menuRenderer, 'renderMenuType'], ['is_safe' => ['html']]),
            new TwigFunction('menu_from_registry', [$this, 'renderMenuTypeFromRegistry'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Renders a menu type stored in the menu type registry.
     *
     * @param string $menuIdentifier
     * @param bool $forceRebuild
     * @return string
     */
    public function renderMenuTypeFromRegistry(string $menuIdentifier, bool $forceRebuild = false): string
    {
        $menuType = $this->menuRegistry->getMenuType($menuIdentifier, $forceRebuild);
        if ($menuType === null)
        {
            throw new LogicException(
                sprintf('Unable to render "%s". This menu type is not present in the menu type registry.', $menuType)
            );
        }

        return $this->menuRenderer->renderMenuType($menuType);
    }
}