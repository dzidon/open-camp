<?php

namespace App\Search\DataStructure;

use App\DataStructure\GraphNodeInterface;
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
     * identifiers divided by a slash. Example: "blog/posts".
     *
     * @param GraphNodeInterface $from
     * @param string $path
     * @return GraphNodeInterface|null
     */
    public function getDescendentByPath(GraphNodeInterface $from, string $path): ?GraphNodeInterface;
}