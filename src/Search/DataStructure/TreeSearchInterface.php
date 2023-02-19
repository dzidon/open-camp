<?php

namespace App\Search\DataStructure;

use App\DataStructure\TreeNodeInterface;

/**
 * Service used for searching trees.
 */
interface TreeSearchInterface
{
    /**
     * Tries to find a descendent of a tree node using a string path. The string should consist of node
     * identifiers divided by a slash. Example: "blog/posts".
     *
     * @param TreeNodeInterface $from
     * @param string $path
     * @return TreeNodeInterface|null
     */
    public function getDescendentByPath(TreeNodeInterface $from, string $path): ?TreeNodeInterface;
}