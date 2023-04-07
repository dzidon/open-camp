<?php

namespace App\DataStructure;

/**
 * Interface for graph nodes that can sort their children.
 */
interface SortableGraphNodeInterface extends GraphNodeInterface
{
    /**
     * Returns null or the sortable parent node.
     *
     * @return SortableGraphNodeInterface|null
     */
    public function getParent(): ?SortableGraphNodeInterface;

    /**
     * Returns a sortable child node using its identifier.
     *
     * @param string $identifier
     * @return SortableGraphNodeInterface|null
     */
    public function getChild(string $identifier): ?SortableGraphNodeInterface;

    /**
     * Sorts its child nodes based on some attribute.
     *
     * @return $this
     */
    public function sortChildren(): self;
}