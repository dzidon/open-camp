<?php

namespace App\Menu\Type;

use App\DataStructure\GraphNodeInterface;

/**
 * Interface for all menus and menu links. Menu types can form a tree structure.
 */
interface MenuTypeInterface extends GraphNodeInterface
{
    /**
     * Returns the displayed text.
     *
     * @return string|null
     */
    public function getText(): ?string;

    /**
     * Sets the displayed text.
     *
     * @param string|null $text
     * @return $this
     */
    public function setText(?string $text): self;

    /**
     * Returns the url.
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Sets the url.
     *
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url): self;

    /**
     * Returns true if the menu type is active.
     *
     * @return bool
     */
    public function isActive(): bool;

    /**
     * Sets the menu type as active/inactive.
     *
     * @param bool $active
     * @param bool $ancestorsToo If true, all ancestors will be set to the specified $active value too.
     * @return $this
     */
    public function setActive(bool $active = true, bool $ancestorsToo = true): self;

    /**
     * Returns the priority of the item among its siblings.
     *
     * @return int
     */
    public function getPriority(): int;

    /**
     * Sets the priority of the item among its siblings.
     *
     * @param int $priority
     * @return $this
     */
    public function setPriority(int $priority): self;

    /**
     * Returns the template block name.
     *
     * @return string
     */
    public function getTemplateBlock(): string;

    /**
     * Sets the template block name.
     *
     * @param string $block
     * @return $this
     */
    public function setTemplateBlock(string $block): self;
}