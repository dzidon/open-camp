<?php

namespace App\Tests\Functional\Search\DataStructure;

use App\EventDispatcher\Event\DepthFirstSearch\ChildIterationEndEvent;
use App\EventDispatcher\Event\DepthFirstSearch\ChildIterationStartEvent;
use App\EventDispatcher\Event\DepthFirstSearch\CycleFoundEvent;
use App\EventDispatcher\Event\DepthFirstSearch\FinishEvent;
use App\EventDispatcher\Event\DepthFirstSearch\InitialPushEvent;
use App\EventDispatcher\Event\DepthFirstSearch\NodeMarkAsExpandedEvent;
use App\EventDispatcher\Event\DepthFirstSearch\StackIterationEndEvent;
use App\EventDispatcher\Event\DepthFirstSearch\StackPopEvent;
use App\Menu\Type\MenuType;
use App\Search\DataStructure\GraphSearch;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Tests the graph search class.
 */
class GraphSearchTest extends TestCase
{
    /**
     * Tests that the depth first search algorithm iterates over graph nodes in the right order.
     *
     * @return void
     */
    public function testDepthFirstSearchOrder(): void
    {
        $tree = $this->createMenuType();
        $treeSearch = $this->createGraphSearch();
        $dispatcher = new EventDispatcher();
        $visitedLog = '';

        $dispatcher->addListener(StackPopEvent::NAME, function (StackPopEvent $event) use (&$visitedLog)
        {
            $identifier = $event->getCurrentNode()->getIdentifier();
            if ($visitedLog === '')
            {
                $visitedLog = $identifier;
            }
            else
            {
                $visitedLog .= ' ' . $identifier;
            }
        });

        $treeSearch->depthFirstSearch($tree, $dispatcher);
        $this->assertSame('menu a b c x y', $visitedLog);
    }

    /**
     * Tests that the depth first search algorithm dispatches all events.
     *
     * @return void
     */
    public function testDepthFirstSearchEvents(): void
    {
        $eventsDispatched = [
            ChildIterationEndEvent::NAME => false,
            ChildIterationStartEvent::NAME => false,
            FinishEvent::NAME => false,
            InitialPushEvent::NAME => false,
            NodeMarkAsExpandedEvent::NAME => false,
            StackIterationEndEvent::NAME => false,
            StackPopEvent::NAME => false,
            CycleFoundEvent::NAME => false,
        ];

        $graph = $this->createMenuType(true);
        $graphSearch = $this->createGraphSearch();
        $dispatcher = new EventDispatcher();

        foreach ($eventsDispatched as $eventName => $dispatched)
        {
            $dispatcher->addListener($eventName, function () use (&$eventsDispatched, $eventName)
            {
                $eventsDispatched[$eventName] = true;
            });
        }

        $graphSearch->depthFirstSearch($graph, $dispatcher);
        $this->assertSame([
            ChildIterationEndEvent::NAME => true,
            ChildIterationStartEvent::NAME => true,
            FinishEvent::NAME => true,
            InitialPushEvent::NAME => true,
            NodeMarkAsExpandedEvent::NAME => true,
            StackIterationEndEvent::NAME => true,
            StackPopEvent::NAME => true,
            CycleFoundEvent::NAME => true,
        ], $eventsDispatched);
    }

    /**
     * Tests that descendents can be looked up using string paths.
     *
     * @return void
     */
    public function testGetDescendentByPath(): void
    {
        $menu = $this->createMenuType();
        $treeSearch = $this->createGraphSearch();

        $itemC = $treeSearch->getDescendentByPath($menu, 'a/b/c');
        $this->assertNotNull($itemC);
        $this->assertSame('c', $itemC->getIdentifier());

        $itemB = $treeSearch->getDescendentByPath($menu, 'a/b');
        $this->assertNotNull($itemB);
        $this->assertSame('b', $itemB->getIdentifier());

        $itemC = $treeSearch->getDescendentByPath($itemB, 'c');
        $this->assertNotNull($itemC);
        $this->assertSame('c', $itemC->getIdentifier());

        $item = $treeSearch->getDescendentByPath($menu, 'a/y');
        $this->assertSame(null, $item);
    }

    /**
     * Creates an instance of the graph search class.
     *
     * @return GraphSearch
     */
    private function createGraphSearch(): GraphSearch
    {
        return new GraphSearch();
    }

    /**
     * Creates a menu type tree structure.
     *
     * @param bool $withCycle
     * @return MenuType
     */
    private function createMenuType(bool $withCycle = false): MenuType
    {
        $menu = new MenuType('menu', 'menu_block');
        $itemA = new MenuType('a', 'item_block');
        $itemB = new MenuType('b', 'item_block');
        $itemC = new MenuType('c', 'item_block');
        $itemX = new MenuType('x', 'item_block');
        $itemY = new MenuType('y', 'item_block');

        $menu
            ->addChild($itemA)
            ->addChild($itemX)
        ;

        $itemA->addChild($itemB);
        $itemB->addChild($itemC);
        $itemX->addChild($itemY);

        if ($withCycle)
        {
            $itemY->addChild($itemX);
        }

        return $menu;
    }
}