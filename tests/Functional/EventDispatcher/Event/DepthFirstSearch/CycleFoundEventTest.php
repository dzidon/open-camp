<?php

namespace App\Tests\Functional\EventDispatcher\Event\DepthFirstSearch;

use App\DataStructure\Stack;
use App\EventDispatcher\Event\DepthFirstSearch\CycleFoundEvent;
use App\Menu\Type\MenuType;
use PHPUnit\Framework\TestCase;

class CycleFoundEventTest extends TestCase
{
    public function testEvent(): void
    {
        $stack = new Stack();
        $childNode = new MenuType('item1', 'block');
        $parentNode = new MenuType('item2', 'block');

        $event = new CycleFoundEvent($stack, ['123'], $childNode, $parentNode);

        $this->assertSame($stack, $event->getStack());
        $this->assertSame(['123'], $event->getExpandedNodes());
        $this->assertSame($childNode, $event->getChildNode());
        $this->assertSame($parentNode, $event->getParentNode());
    }
}