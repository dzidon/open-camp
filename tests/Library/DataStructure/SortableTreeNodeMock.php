<?php

namespace App\Tests\Library\DataStructure;

use App\Library\DataStructure\TreeNodeInterface;
use App\Library\DataStructure\SortableTreeNodeInterface;
use LogicException;

/**
 * Tree node mock with sortable children.
 */
class SortableTreeNodeMock extends TreeNodeMock implements SortableTreeNodeInterface
{
    protected int $priority = 0;

    /**
     * Returns a sortable child node using its identifier.
     *
     * @param string $identifier
     * @return SortableTreeNodeInterface|null
     */
    public function getChild(string $identifier): ?SortableTreeNodeInterface
    {
        return parent::getChild($identifier);
    }

    /**
     * Return the sortable parent tree node.
     *
     * @return SortableTreeNodeInterface|null
     */
    public function getParent(): ?SortableTreeNodeInterface
    {
        return parent::getParent();
    }

    /**
     * Sets parent node.
     *
     * @param SortableTreeNodeMock|null $parent
     * @return self
     */
    public function setParent(?TreeNodeInterface $parent): self
    {
        $this->assertSelfReferencedType($parent);

        return parent::setParent($parent);
    }

    /**
     * Adds a child node.
     *
     * @param SortableTreeNodeMock $child
     * @return self
     */
    public function addChild(TreeNodeInterface $child): self
    {
        $this->assertSelfReferencedType($child);

        return parent::addChild($child);
    }

    /**
     * Removes a child node.
     *
     * @param SortableTreeNodeMock|string $child
     * @return self
     */
    public function removeChild(TreeNodeInterface|string $child): self
    {
        $this->assertSelfReferencedType($child);

        return parent::removeChild($child);
    }

    /**
     * Gets priority for sorting.
     *
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Sets priority for sorting.
     *
     * @param int $priority
     * @return $this
     */
    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Sorts child nodes using the priority attribute in descending order.
     *
     * @return $this
     */
    public function sortChildren(): self
    {
        if (!empty($this->children))
        {
            usort($this->children, function (SortableTreeNodeMock $a, SortableTreeNodeMock $b)
            {
                return $b->getPriority() <=> $a->getPriority();
            });
        }

        return $this;
    }

    /**
     * Throws a LogicException if the specified node type is not supported in a child/parent relationship.
     *
     * @param mixed $treeNode
     * @return void
     */
    protected function assertSelfReferencedType(mixed $treeNode): void
    {
        if (is_object($treeNode) && !$treeNode instanceof SortableTreeNodeMock)
        {
            throw new LogicException(
                sprintf('%s cannot be used as a parent/child with %s.', $treeNode::class, $this::class)
            );
        }
    }
}