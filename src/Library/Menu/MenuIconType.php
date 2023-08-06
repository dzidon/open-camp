<?php

namespace App\Library\Menu;

/**
 * Main implementation of a menu type displaying an icon.
 */
class MenuIconType extends MenuType implements MenuIconTypeInterface
{
    private ?string $icon;

    public function __construct(string $identifier, string $block, ?string $text = null,
                                string $url = '#', ?string $icon = null)
    {
        parent::__construct($identifier, $block, $text, $url);
        $this->icon = $icon;
    }

    /**
     * @inheritDoc
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @inheritDoc
     */
    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }
}