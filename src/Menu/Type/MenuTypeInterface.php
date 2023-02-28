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
     * Sets the parent menu type.
     *
     * @param MenuTypeInterface|null $parent
     * @return $this
     */
    public function setParent(?MenuTypeInterface $parent): self;

    /**
     * Adds a child menu type.
     *
     * @param MenuTypeInterface $child
     * @return $this
     */
    public function addChild(MenuTypeInterface $child): self;

    /**
     * Removes a child menu type.
     *
     * @param string|MenuTypeInterface $child
     * @return $this
     */
    public function removeChild(string|MenuTypeInterface $child): self;

    /**
     * Returns a child menu type using its identifier.
     *
     * @param string $identifier
     * @return MenuTypeInterface|null
     */
    public function getChild(string $identifier): ?MenuTypeInterface;

    /**
     * Returns true if the menu type has a child with the specified identifier.
     *
     * @param string $identifier
     * @return bool
     */
    public function hasChild(string $identifier): bool;

    /**
     * Sorts its children using the priority attribute in descending order.
     *
     * @return $this
     */
    public function sortChildren(): self;

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