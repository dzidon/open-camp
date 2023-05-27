<?php

namespace App\Twig;

use App\Menu\Renderer\MenuTypeRendererInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Adds menu type rendering functionality to Twig.
 */
class MenuTypeRendererExtension extends AbstractExtension
{
    private MenuTypeRendererInterface $menuRenderer;

    public function __construct(MenuTypeRendererInterface $menuRenderer)
    {
        $this->menuRenderer = $menuRenderer;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('menu', [$this->menuRenderer, 'renderMenuType'], ['is_safe' => ['html']]),
        ];
    }
}