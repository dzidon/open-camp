<?php

namespace App\Menu\Renderer;

use App\Menu\Type\MenuTypeInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;

/**
 * @inheritDoc
 */
class MenuTypeRenderer implements MenuTypeRendererInterface
{
    private Environment $twig;
    private ParameterBagInterface $parameterBag;

    public function __construct(Environment $twig, ParameterBagInterface $parameterBag)
    {
        $this->twig = $twig;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @inheritDoc
     */
    public function renderMenuType(MenuTypeInterface $menuType): string
    {
        $menuTheme = $this->parameterBag->get('app_menu_theme');
        $template = $this->twig->load($menuTheme);
        return $template->renderBlock($menuType->getTemplateBlock(), ['menu_type' => $menuType]);
    }
}