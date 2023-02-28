<?php

namespace App\EventDispatcher\Event\DepthFirstSearch;

use App\DataStructure\StackInterface;
use App\DataStructure\GraphNodeInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Dispatched at the end of a child iteration.
 */
class ChildIterationEndEvent extends Event
{
    public const NAME = 'dfs.child_iteration_end';

    private StackInterface $stack;
    private array $expandedNodes;
    private GraphNodeInterface $childNode;
    private GraphNodeInterface $parentNode;

    public function __construct(StackInterface $stack, array $expandedNodes, GraphNodeInterface $childNode, GraphNodeInterface $parentNode)
    {
        $this->stack = $stack;
        $this->expandedNodes = $expandedNodes;
        $this->childNode = $childNode;
        $this->parentNode = $parentNode;
    }

    public function getStack(): StackInterface
    {
        return $this->stack;
    }

    public function getExpandedNodes(): array
    {
        return $this->expandedNodes;
    }

    public function getChildNode(): GraphNodeInterface
    {
        return $this->childNode;
    }

    public function getParentNode(): GraphNodeInterface
    {
        return $this->parentNode;
    }
}