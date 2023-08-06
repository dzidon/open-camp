<?php

namespace App\Library\Event\Search\DepthFirstSearch;

use App\Library\DataStructure\TreeNodeInterface;
use App\Library\DataStructure\StackInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Dispatched after a node is marked as expanded (already visited).
 */
class NodeMarkAsExpandedEvent extends Event
{
    public const NAME = 'dfs.node_mark_as_expanded';

    private array $expandedNodes;
    private StackInterface $stack;
    private TreeNodeInterface $currentNode;

    public function __construct(StackInterface $stack, array $expandedNodes, TreeNodeInterface $currentNode)
    {
        $this->stack = $stack;
        $this->expandedNodes = $expandedNodes;
        $this->currentNode = $currentNode;
    }

    public function getStack(): StackInterface
    {
        return $this->stack;
    }

    public function getExpandedNodes(): array
    {
        return $this->expandedNodes;
    }

    public function getCurrentNode(): TreeNodeInterface
    {
        return $this->currentNode;
    }
}