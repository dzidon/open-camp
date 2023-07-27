<?php

namespace App\Search\DataStructure;

use App\DataStructure\GraphNodeInterface;
use App\DataStructure\SortableGraphNodeInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Service used for searching in graphs.
 */
interface GraphSearchInterface
{
    /**
     * Performs the depth first search.
     *
     * @param GraphNodeInterface $start
     * @param EventDispatcherInterface $eventDispatcher
     * @return void
     */
    public function depthFirstSearch(GraphNodeInterface $start, EventDispatcherInterface $eventDispatcher): void;

    /**
     * Returns true if a cycle is found in a graph.
     *
     * @param GraphNodeInterface $start
     * @return bool
     */
    public function containsCycle(GraphNodeInterface $start): bool;

    /**
     * Tries to find a descendent of a graph node using a string path. The string should consist of node
     * identifiers divided by a slash. Example: "blog/posts". The start ("from") node is not included in the
     * path.
     *
     * @param GraphNodeInterface $from
     * @param string $path
     * @param string $property
     * @return GraphNodeInterface|null
     */
    public function getDescendentByPath(GraphNodeInterface $from, string $path, string $property = 'identifier'): ?GraphNodeInterface;

    /**
     * Returns all descendents for the given node.
     *
     * @param GraphNodeInterface $node
     * @return array
     */
    public function getDescendentsOfNode(GraphNodeInterface $node): array;

    /**
     * Sorts all child nodes recursively using some attribute.
     *
     * @param SortableGraphNodeInterface $start
     * @return void
     */
    public function sortChildrenRecursively(SortableGraphNodeInterface $start): void;
}