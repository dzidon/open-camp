<?php

namespace App\Tests\Library\DataStructure;

use App\Library\DataStructure\TreeNodeInterface;

/**
 * Trait that helps to work with tree node children identifiers.
 */
trait TreeNodeChildrenIdentifiersTrait
{
    /**
     * Returns an array containing identifiers of children of a given tree node.
     *
     * @param TreeNodeInterface $node
     * @return array
     */
    private function getTreeNodeChildrenIdentifiers(TreeNodeInterface $node): array
    {
        $identifiers = [];
        foreach ($node->getChildren() as $child)
        {
            $identifiers[] = $child->getIdentifier();
        }

        return $identifiers;
    }
}