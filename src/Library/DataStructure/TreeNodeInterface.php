<?php

namespace App\Library\DataStructure;

/**
 * Interface for all tree nodes.
 */
interface TreeNodeInterface
{
    /**
     * Returns a unique identifier of the node.
     *
     * @return mixed
     */
    public function getIdentifier(): mixed;

    /**
     * Returns null or the parent node.
     *
     * @return TreeNodeInterface|null
     */
    public function getParent(): ?TreeNodeInterface;

    /**
     * Sets the parent node.
     *
     * @param TreeNodeInterface|null $parent
     * @return $this
     */
    public function setParent(?TreeNodeInterface $parent): self;

    /**
     * Returns all child nodes.
     *
     * @return TreeNodeInterface[]
     */
    public function getChildren(): array;

    /**
     * Adds a child node.
     *
     * @param TreeNodeInterface $child
     * @return $this
     */
    public function addChild(TreeNodeInterface $child): self;

    /**
     * Removes a child node.
     *
     * @param string|TreeNodeInterface $child Identifier or instance.
     * @return $this
     */
    public function removeChild(string|TreeNodeInterface $child): self;

    /**
     * Returns a child node using its identifier.
     *
     * @param string $identifier
     * @return TreeNodeInterface|null
     */
    public function getChild(string $identifier): ?TreeNodeInterface;

    /**
     * Returns true if the node has a child with the specified identifier.
     *
     * @param string $identifier
     * @return bool
     */
    public function hasChild(string $identifier): bool;
}