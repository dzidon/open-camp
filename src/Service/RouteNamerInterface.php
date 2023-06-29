<?php

namespace App\Service;

/**
 * Sets and gets the current route name. The current route name is displayed in the title, and in the h1 heading.
 */
interface RouteNamerInterface
{
    /**
     * Returns true if the current route name is a non-empty string.
     *
     * @return bool
     */
    public function isCurrentRouteNameSet(): bool;

    /**
     * Returns the current route name or null.
     *
     * @return string|null
     */
    public function getCurrentRouteName(): ?string;

    /**
     * Returns the full page title. Useful in the HTML title tag.
     *
     * @return string
     */
    public function getCurrentTitle(): string;

    /**
     * Automatically sets the current route name based on the "_route" attribute from the current request.
     *
     * @return void
     */
    public function setCurrentRouteNameByRequest(): void;

    /**
     * Sets the current route name using route identifier.
     *
     * @param string $route
     * @return void
     */
    public function setCurrentRouteNameByRoute(string $route): void;

    /**
     * Sets the current route name to a specific string.
     *
     * @param string|null $name
     * @return void
     */
    public function setCurrentRouteName(?string $name): void;

    /**
     * Appends string to the current route name.
     *
     * @param string $string
     * @param bool $addSpace
     * @return void
     */
    public function appendToCurrentRouteName(string $string, bool $addSpace = true): void;

    /**
     * Prepends string to the current route name.
     *
     * @param string $string
     * @param bool $addSpace
     * @return void
     */
    public function prependToCurrentRouteName(string $string, bool $addSpace = true): void;
}