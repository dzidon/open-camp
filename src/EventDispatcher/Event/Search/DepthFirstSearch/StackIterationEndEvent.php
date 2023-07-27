<?php

namespace App\EventDispatcher\Event\Search\DepthFirstSearch;

use App\DataStructure\GraphNodeInterface;
use App\DataStructure\StackInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Dispatched at the end of a stack iteration.
 */
class StackIterationEndEvent extends Event
{
    public const NAME = 'dfs.stack_iteration_end';

    private StackInterface $stack;
    private array $expandedNodes;
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