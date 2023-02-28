<?php

namespace App\DataStructure;

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
     * Returns all child nodes.
     *
     * @return GraphNodeInterface[]
     */
    public function getChildren(): array;
}