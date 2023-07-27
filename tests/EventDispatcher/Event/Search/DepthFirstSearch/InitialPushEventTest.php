<?php

namespace App\Tests\EventDispatcher\Event\Search\DepthFirstSearch;

use App\DataStructure\Stack;
use App\EventDispatcher\Event\Search\DepthFirstSearch\InitialPushEvent;
use PHPUnit\Framework\TestCase;

class InitialPushEventTest extends TestCase
{
    public function testEvent(): void
    {
        $stack = new Stack();
        $event = new InitialPushEvent($stack);

        $this->assertSame($stack, $event->getStack());
    }
}