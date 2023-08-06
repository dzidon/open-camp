<?php

namespace App\Tests\Library\Event\Search\DepthFirstSearch;

use App\Library\DataStructure\Stack;
use App\Library\Event\Search\DepthFirstSearch\NodeMarkAsExpandedEvent;
use App\Library\Menu\MenuType;
use PHPUnit\Framework\TestCase;

class NodeMarkAsExpandedEventTest extends TestCase
{
    public function testEvent(): void
    {
        $stack = new Stack();
        $currentNode = new MenuType('item1', 'block');

        $event = new NodeMarkAsExpandedEvent($stack, ['123'], $currentNode);

        $this->assertSame($stack, $event->getStack());
        $this->assertSame(['123'], $event->getExpandedNodes());
        $this->assertSame($currentNode, $event->getCurrentNode());
    }
}