<?php

namespace App\Tests\Functional\EventDispatcher\Event\DepthFirstSearch;

use App\DataStructure\Stack;
use App\EventDispatcher\Event\DepthFirstSearch\StackPopEvent;
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