<?php

namespace App\EventDispatcher\Event\Search\DepthFirstSearch;

use App\DataStructure\GraphNodeInterface;
use App\DataStructure\StackInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Dispatched at the beginning of a child iteration.
 */
class ChildIterationStartEvent extends Event
{
    public const NAME = 'dfs.child_iteration_start';

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