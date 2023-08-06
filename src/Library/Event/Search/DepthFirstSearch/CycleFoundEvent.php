<?php

namespace App\Library\Event\Search\DepthFirstSearch;

use App\Library\DataStructure\TreeNodeInterface;
use App\Library\DataStructure\StackInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Dispatched when a cycle is found in a tree.
 */
class CycleFoundEvent extends Event
{
    public const NAME = 'dfs.cycle_found';

    private StackInterface $stack;
    private array $expandedNodes;
    private TreeNodeInterface $childNode;
    private TreeNodeInterface $parentNode;

    public function __construct(StackInterface $stack, array $expandedNodes, TreeNodeInterface $childNode, TreeNodeInterface $parentNode)
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

    public function getChildNode(): TreeNodeInterface
    {
        return $this->childNode;
    }

    public function getParentNode(): TreeNodeInterface
    {
        return $this->parentNode;
    }
}