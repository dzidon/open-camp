<?php

namespace App\EventDispatcher\Event\DepthFirstSearch;

use App\DataStructure\StackInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Dispatched after the initial push into the stack.
 */
class InitialPushEvent extends Event
{
    public const NAME = 'dfs.initial_push';

    private StackInterface $stack;

    public function __construct(StackInterface $stack)
    {
        $this->stack = $stack;
    }

    public function getStack(): StackInterface
    {
        return $this->stack;
    }
}