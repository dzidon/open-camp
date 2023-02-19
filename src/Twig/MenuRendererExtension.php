<?php

namespace App\Twig;

use App\Menu\Registry\MenuRegistryInterface;
use App\Menu\Type\MenuTypeInterface;
use LogicException;
use Throwable;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Adds menu rendering functions to Twig.
 */
class MenuRendererExtension extends AbstractExtension
{
    private MenuRegistryInterface $menuRegistry;

    public function __construct(MenuRegistryInterface $menuRegistry)
    {
        $this->menuRegistry = $menuRegistry;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('menu', [$this, 'renderMenu'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    /**
     * Renders a menu type.
     *
     * @param Environment $twig
     * @param string|MenuTypeInterface $menuType Identifier used in the central menu registry or a specific menu type
     *                                           instance.
     * @param bool $forceRebuild
     * @return string
     * @throws Throwable
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderMenu(Environment $twig, string|MenuTypeInterface $menuType, bool $forceRebuild = false): string
    {
        if (is_string($menuType))
        {
            $menu = $this->menuRegistry->getMenuType($menuType, $forceRebuild);
            if ($menu === null)
            {
                throw new LogicException(sprintf('Unable to render "%s". This menu is not present in the menu registry.', $menuType));
            }

            $menuType = $menu;
        }

        $template = $twig->load('_fragments/_menu_layout.html.twig');
        return $template->renderBlock($menuType->getTemplateBlock(), ['menu_type' => $menuType]);
    }
}