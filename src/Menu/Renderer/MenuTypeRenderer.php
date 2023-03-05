<?php

namespace App\Menu\Renderer;

use App\Menu\Type\MenuTypeInterface;
use Twig\Environment;

/**
 * @inheritDoc
 */
class MenuTypeRenderer implements MenuTypeRendererInterface
{
    private Environment $twig;
    private string $menuTheme;

    public function __construct(Environment $twig, string $menuTheme)
    {
        $this->twig = $twig;
        $this->menuTheme = $menuTheme;
    }

    /**
     * @inheritDoc
     */
    public function renderMenuType(MenuTypeInterface $menuType): string
    {
        $template = $this->twig->load($this->menuTheme);
        return $template->renderBlock($menuType->getTemplateBlock(), [
            'menu_type' => $menuType,
            'menu_theme' => $this->menuTheme,
        ]);
    }
}