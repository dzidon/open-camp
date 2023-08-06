<?php

namespace App\Library\Event\Search\DepthFirstSearch;

use App\Library\DataStructure\TreeNodeInterface;
use App\Library\DataStructure\StackInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Dispatched at the end of a stack iteration.
 */
class StackIterationEndEvent extends Event
{
    public const NAME = 'dfs.stack_iteration_end';

    private StackInterface $stack;
    private array $expandedNodes;
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