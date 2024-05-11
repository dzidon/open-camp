<?php

namespace App\Service\Menu\Renderer;

use App\Library\Menu\MenuTypeInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Twig\Environment;

/**
 * @inheritDoc
 */
class MenuTypeRenderer implements MenuTypeRendererInterface
{
    private Environment $twig;

    private string $menuTheme;

    public function __construct(
        Environment $twig,

        #[Autowire('%app.menu_theme%')]
        string $menuTheme
    ) {
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