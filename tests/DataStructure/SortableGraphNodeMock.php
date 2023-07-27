<?php

namespace App\Tests\DataStructure;

use App\DataStructure\GraphNodeInterface;
use App\DataStructure\SortableGraphNodeInterface;
use LogicException;

/**
 * Graph node mock with sortable children.
 */
class SortableGraphNodeMock extends GraphNodeMock implements SortableGraphNodeInterface
{
    protected int $priority = 0;

    /**
     * Returns a sortable child node using its identifier.
     *
     * @param string $identifier
     * @return SortableGraphNodeInterface|null
     */
    public function getChild(string $identifier): ?SortableGraphNodeInterface
    {
        return parent::getChild($identifier);
    }

    /**
     * Return the sortable parent graph node.
     *
     * @return SortableGraphNodeInterface|null
     */
    public function getParent(): ?SortableGraphNodeInterface
    {
        return parent::getParent();
    }

    /**
     * Sets parent node.
     *
     * @param SortableGraphNodeMock|null $parent
     * @return self
     */
    public function setParent(?GraphNodeInterface $parent): self
    {
        $this->assertSelfReferencedType($parent);

        return parent::setParent($parent);
    }

    /**
     * Adds a child node.
     *
     * @param SortableGraphNodeMock $child
     * @return self
     */
    public function addChild(GraphNodeInterface $child): self
    {
        $this->assertSelfReferencedType($child);

        return parent::addChild($child);
    }

    /**
     * Removes a child node.
     *
     * @param SortableGraphNodeMock|string $child
     * @return self
     */
    public function removeChild(GraphNodeInterface|string $child): self
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
            usort($this->children, function (SortableGraphNodeMock $a, SortableGraphNodeMock $b)
            {
                return $b->getPriority() <=> $a->getPriority();
            });
        }

        return $this;
    }

    /**
     * Throws a LogicException if the specified node type is not supported in a child/parent relationship.
     *
     * @param mixed $graphNode
     * @return void
     */
    protected function assertSelfReferencedType(mixed $graphNode): void
    {
        if (is_object($graphNode) && !$graphNode instanceof SortableGraphNodeMock)
        {
            throw new LogicException(
                sprintf('%s cannot be used as a parent/child with %s.', $graphNode::class, $this::class)
            );
        }
    }
}