<?php

namespace App\Service\Theme;

/**
 * Helps with retrieving info regarding theme configuration.
 */
interface ThemeConfigHelperInterface
{
    /**
     * Returns the default theme or null if no themes are supported.
     *
     * @return string|null
     */
    public function getDefaultTheme(): ?string;

    /**
     * Returns true if the given theme is supported in the app.
     *
     * @param string $theme
     * @return bool
     */
    public function isValidTheme(string $theme): bool;
}