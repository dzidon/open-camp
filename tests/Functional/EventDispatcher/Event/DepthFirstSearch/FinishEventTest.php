<?php

namespace App\Tests\Functional\EventDispatcher\Event\DepthFirstSearch;

use App\DataStructure\Stack;
use App\EventDispatcher\Event\DepthFirstSearch\FinishEvent;
use PHPUnit\Framework\TestCase;

class FinishEventTest extends TestCase
{
    public function testEvent(): void
    {
        $stack = new Stack();
        $event = new FinishEvent($stack, ['123']);

        $this->assertSame($stack, $event->getStack());
        $this->assertSame(['123'], $event->getExpandedNodes());
    }
}