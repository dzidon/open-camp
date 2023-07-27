<?php

namespace App\EventDispatcher\Event\Search\DepthFirstSearch;

use App\DataStructure\StackInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Dispatched at the end of depth first search.
 */
class FinishEvent extends Event
{
    public const NAME = 'dfs.finish';

    private StackInterface $stack;
    private array $expandedNodes;

    public function __construct(StackInterface $stack, array $expandedNodes)
    {
        $this->stack = $stack;
        $this->expandedNodes = $expandedNodes;
    }

    public function getStack(): StackInterface
    {
        return $this->stack;
    }

    public function getExpandedNodes(): array
    {
        return $this->expandedNodes;
    }
}