<?php

namespace App\Search\DataStructure;

use App\DataStructure\TreeNodeInterface;

/**
 * @inheritDoc
 */
class TreeSearch implements TreeSearchInterface
{
    /**
     * @inheritDoc
     */
    public function getDescendentByPath(TreeNodeInterface $from, string $path): ?TreeNodeInterface
    {
        $names = explode('/', trim($path, '/'));
        if (empty($names))
        {
            return null;
        }

        $currentNode = $from;

        while (true)
        {
            if (empty($names) || $currentNode === null)
            {
                return $currentNode;
            }

            $currentKey = array_key_first($names);
            $currentName = $names[$currentKey];
            $foundChild = false;

            foreach ($currentNode->getChildren() as $child)
            {
                if ($child->getIdentifier() === $currentName)
                {
                    $currentNode = $child;
                    $foundChild = true;
                    break;
                }
            }

            if (!$foundChild)
            {
                $currentNode = null;
            }

            unset($names[$currentKey]);
        }
    }
}