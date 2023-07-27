<?php

namespace App\Tests\EventDispatcher\Event\Search\DepthFirstSearch;

use App\DataStructure\Stack;
use App\EventDispatcher\Event\Search\DepthFirstSearch\StackPopEvent;
use App\Menu\Type\MenuType;
use PHPUnit\Framework\TestCase;

class StackPopEventTest extends TestCase
{
    public function testEvent(): void
    {
        $stack = new Stack();
        $currentNode = new MenuType('item1', 'block');

        $event = new StackPopEvent($stack, ['123'], $currentNode);

        $this->assertSame($stack, $event->getStack());
        $this->assertSame(['123'], $event->getExpandedNodes());
        $this->assertSame($currentNode, $event->getCurrentNode());
    }
}