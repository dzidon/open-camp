<?php

namespace App\Tests\EventDispatcher\Event\Search\DepthFirstSearch;

use App\DataStructure\Stack;
use App\EventDispatcher\Event\Search\DepthFirstSearch\StackIterationEndEvent;
use App\Menu\Type\MenuType;
use PHPUnit\Framework\TestCase;

class StackIterationEndEventTest extends TestCase
{
    public function testEvent(): void
    {
        $stack = new Stack();
        $currentNode = new MenuType('item1', 'block');

        $event = new StackIterationEndEvent($stack, ['123'], $currentNode);

        $this->assertSame($stack, $event->getStack());
        $this->assertSame(['123'], $event->getExpandedNodes());
        $this->assertSame($currentNode, $event->getCurrentNode());
    }
}