<?php

namespace App\Tests\Library\Event\Search\DepthFirstSearch;

use App\Library\DataStructure\Stack;
use App\Library\Event\Search\DepthFirstSearch\InitialPushEvent;
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