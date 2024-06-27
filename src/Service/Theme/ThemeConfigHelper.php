<?php

namespace App\Service\Theme;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @inheritDoc
 */
class ThemeConfigHelper implements ThemeConfigHelperInterface
{
    private array $themes;

    public function __construct(
        #[Autowire('%app.themes%')]
        array $themes
    ) {
        $this->themes = $themes;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultTheme(): ?string
    {
        if (empty($this->themes))
        {
            return null;
        }

        return $this->themes[array_key_first($this->themes)];
    }

    /**
     * @inheritDoc
     */
    public function isValidTheme(string $theme): bool
    {
        return in_array($theme, $this->themes);
    }
}