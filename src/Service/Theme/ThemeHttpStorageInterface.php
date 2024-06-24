<?php

namespace App\Service\Theme;

use Symfony\Component\HttpFoundation\Response;

/**
 * Can be used to get and set user's UI theme (dark mode, light mode, ...).
 */
interface ThemeHttpStorageInterface
{
    /**
     * Returns the current UI theme or null if it's invalid or not yet set.
     *
     * @return string|null
     */
    public function getCurrentTheme(): ?string;

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

    /**
     * Sets the UI theme. Throws a LogicException if the given theme is unsupported.
     *
     * @param string $theme
     * @param Response $response
     * @return void
     */
    public function setTheme(string $theme, Response $response): void;
}