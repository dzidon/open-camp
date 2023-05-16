<?php

namespace App\Tests\EventDispatcher\Event\DepthFirstSearch;

use App\DataStructure\Stack;
use App\EventDispatcher\Event\DepthFirstSearch\InitialPushEvent;
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