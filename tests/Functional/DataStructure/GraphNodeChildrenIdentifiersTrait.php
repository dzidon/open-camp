<?php

namespace App\Tests\Functional\DataStructure;

use App\DataStructure\GraphNodeInterface;

/**
 * Trait that helps to work with graph node children identifiers.
 */
trait GraphNodeChildrenIdentifiersTrait
{
    /**
     * Returns an array containing identifiers of children of a given graph node.
     *
     * @param GraphNodeInterface $node
     * @return array
     */
    private function getGraphNodeChildrenIdentifiers(GraphNodeInterface $node): array
    {
        $identifiers = [];
        foreach ($node->getChildren() as $child)
        {
            $identifiers[] = $child->getIdentifier();
        }

        return $identifiers;
    }
}