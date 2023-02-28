<?php

namespace App\EventDispatcher\Event\DepthFirstSearch;

use App\DataStructure\StackInterface;
use App\DataStructure\GraphNodeInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Dispatched when a cycle is found in a graph.
 */
class CycleFoundEvent extends Event
{
    public const NAME = 'dfs.cycle_found';

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