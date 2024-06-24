<?php

namespace App\Service\Twig;

use App\Service\Theme\ThemeHttpStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Adds theme functions to Twig.
 */
class ThemeExtension extends AbstractExtension
{
    private ThemeHttpStorageInterface $themeHttpStorage;

    public function __construct(ThemeHttpStorageInterface $themeHttpStorage)
    {
        $this->themeHttpStorage = $themeHttpStorage;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_theme', [$this, 'getTheme']),
        ];
    }

    public function getTheme(): ?string
    {
        $theme = $this->themeHttpStorage->getCurrentTheme();

        if ($theme === null)
        {
            $theme = $this->themeHttpStorage->getDefaultTheme();
        }

        return $theme;
    }
}