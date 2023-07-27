<?php

namespace App\Tests\EventDispatcher\Event\Search\DepthFirstSearch;

use App\DataStructure\Stack;
use App\EventDispatcher\Event\Search\DepthFirstSearch\ChildIterationStartEvent;
use App\Menu\Type\MenuType;
use PHPUnit\Framework\TestCase;

class ChildIterationStartEventTest extends TestCase
{
    public function testEvent(): void
    {
        $stack = new Stack();
        $childNode = new MenuType('item1', 'block');
        $parentNode = new MenuType('item2', 'block');

        $event = new ChildIterationStartEvent($stack, ['123'], $childNode, $parentNode);

        $this->assertSame($stack, $event->getStack());
        $this->assertSame(['123'], $event->getExpandedNodes());
        $this->assertSame($childNode, $event->getChildNode());
        $this->assertSame($parentNode, $event->getParentNode());
    }
}