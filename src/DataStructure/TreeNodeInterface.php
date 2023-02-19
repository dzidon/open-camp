<?php

namespace App\DataStructure;

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
     * Returns all child nodes.
     *
     * @return TreeNodeInterface[]
     */
    public function getChildren(): array;
}