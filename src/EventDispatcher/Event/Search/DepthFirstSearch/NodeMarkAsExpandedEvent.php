<?php

namespace App\EventDispatcher\Event\Search\DepthFirstSearch;

use App\DataStructure\GraphNodeInterface;
use App\DataStructure\StackInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Dispatched after a node is marked as expanded (already visited).
 */
class NodeMarkAsExpandedEvent extends Event
{
    public const NAME = 'dfs.node_mark_as_expanded';

    private array $expandedNodes;
    private StackInterface $stack;
    private GraphNodeInterface $currentNode;

    public function __construct(StackInterface $stack, array $expandedNodes, GraphNodeInterface $currentNode)
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

    public function getCurrentNode(): GraphNodeInterface
    {
        return $this->currentNode;
    }
}