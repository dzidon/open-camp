<?php

namespace App\Library\DataStructure;

/**
 * Interface for tree nodes that can sort their children.
 */
interface SortableTreeNodeInterface extends TreeNodeInterface
{
    /**
     * Returns null or the sortable parent node.
     *
     * @return SortableTreeNodeInterface|null
     */
    public function getParent(): ?SortableTreeNodeInterface;

    /**
     * Returns a sortable child node using its identifier.
     *
     * @param string $identifier
     * @return SortableTreeNodeInterface|null
     */
    public function getChild(string $identifier): ?SortableTreeNodeInterface;

    /**
     * Sorts its child nodes based on some attribute.
     *
     * @return $this
     */
    public function sortChildren(): self;
}