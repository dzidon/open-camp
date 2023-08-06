<?php

namespace App\Library\DataStructure;

/**
 * Interface for all graph nodes.
 */
interface GraphNodeInterface
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
     * @return GraphNodeInterface|null
     */
    public function getParent(): ?GraphNodeInterface;

    /**
     * Sets the parent node.
     *
     * @param GraphNodeInterface|null $parent
     * @return $this
     */
    public function setParent(?GraphNodeInterface $parent): self;

    /**
     * Returns all child nodes.
     *
     * @return GraphNodeInterface[]
     */
    public function getChildren(): array;

    /**
     * Adds a child node.
     *
     * @param GraphNodeInterface $child
     * @return $this
     */
    public function addChild(GraphNodeInterface $child): self;

    /**
     * Removes a child node.
     *
     * @param string|GraphNodeInterface $child Identifier or instance.
     * @return $this
     */
    public function removeChild(string|GraphNodeInterface $child): self;

    /**
     * Returns a child node using its identifier.
     *
     * @param string $identifier
     * @return GraphNodeInterface|null
     */
    public function getChild(string $identifier): ?GraphNodeInterface;

    /**
     * Returns true if the node has a child with the specified identifier.
     *
     * @param string $identifier
     * @return bool
     */
    public function hasChild(string $identifier): bool;
}