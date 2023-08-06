<?php

namespace App\Service\Search\DataStructure;

use App\Library\DataStructure\TreeNodeInterface;
use App\Library\DataStructure\SortableTreeNodeInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Service used for searching in trees.
 */
interface TreeSearchInterface
{
    /**
     * Performs the depth first search.
     *
     * @param TreeNodeInterface $start
     * @param EventDispatcherInterface $eventDispatcher
     * @return void
     */
    public function depthFirstSearch(TreeNodeInterface $start, EventDispatcherInterface $eventDispatcher): void;

    /**
     * Returns true if a cycle is found in a tree.
     *
     * @param TreeNodeInterface $start
     * @return bool
     */
    public function containsCycle(TreeNodeInterface $start): bool;

    /**
     * Tries to find a descendent of a tree node using a string path. The string should consist of node
     * identifiers divided by a slash. Example: "blog/posts". The start ("from") node is not included in the
     * path.
     *
     * @param TreeNodeInterface $from
     * @param string $path
     * @param string $property
     * @return TreeNodeInterface|null
     */
    public function getDescendentByPath(TreeNodeInterface $from, string $path, string $property = 'identifier'): ?TreeNodeInterface;

    /**
     * Returns all descendents for the given node.
     *
     * @param TreeNodeInterface $node
     * @return array
     */
    public function getDescendentsOfNode(TreeNodeInterface $node): array;

    /**
     * Sorts all child nodes recursively using some attribute.
     *
     * @param SortableTreeNodeInterface $start
     * @return void
     */
    public function sortChildrenRecursively(SortableTreeNodeInterface $start): void;
}