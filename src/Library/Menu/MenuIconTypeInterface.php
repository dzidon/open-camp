<?php

namespace App\Library\Menu;

/**
 * Interface for menu types that display an icon.
 */
interface MenuIconTypeInterface extends MenuTypeInterface
{
    /**
     * Returns the icon name.
     *
     * @return string|null
     */
    public function getIcon(): ?string;

    /**
     * Sets the icon name.
     *
     * @param string|null $icon
     * @return $this
     */
    public function setIcon(?string $icon): self;
}