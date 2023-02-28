<?php

namespace App\EventDispatcher\Event\DepthFirstSearch;

use App\DataStructure\StackInterface;
use App\DataStructure\GraphNodeInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Dispatched after a node is popped out of the stack.
 */
class StackPopEvent extends Event
{
    public const NAME = 'dfs.stack_pop';

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