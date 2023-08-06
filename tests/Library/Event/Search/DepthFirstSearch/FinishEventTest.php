<?php

namespace App\Tests\Library\Event\Search\DepthFirstSearch;

use App\Library\DataStructure\Stack;
use App\Library\Event\Search\DepthFirstSearch\FinishEvent;
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