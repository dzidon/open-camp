<?php

namespace App\Tests\Search\DataStructure;

use App\EventDispatcher\Event\DepthFirstSearch\ChildIterationEndEvent;
use App\EventDispatcher\Event\DepthFirstSearch\ChildIterationStartEvent;
use App\EventDispatcher\Event\DepthFirstSearch\CycleFoundEvent;
use App\EventDispatcher\Event\DepthFirstSearch\FinishEvent;
use App\EventDispatcher\Event\DepthFirstSearch\InitialPushEvent;
use App\EventDispatcher\Event\DepthFirstSearch\NodeMarkAsExpandedEvent;
use App\EventDispatcher\Event\DepthFirstSearch\StackIterationEndEvent;
use App\EventDispatcher\Event\DepthFirstSearch\StackPopEvent;
use App\Search\DataStructure\GraphSearch;
use App\Tests\DataStructure\GraphNodeChildrenIdentifiersTrait;
use App\Tests\DataStructure\GraphNodeMock;
use App\Tests\DataStructure\SortableGraphNodeMock;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Tests the graph search class.
 */
class GraphSearchTest extends KernelTestCase
{
    use GraphNodeChildrenIdentifiersTrait;

    /**
     * Tests that the depth first search algorithm iterates over graph nodes in the right order.
     *
     * @return void
     * @throws Exception
     */
    public function testDepthFirstSearchOrder(): void
    {
        $tree = $this->createGraphNodeMock();
        $treeSearch = $this->getGraphSearch();
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
        $this->assertSame('root a b c x y', $visitedLog);
    }

    /**
     * Tests that the depth first search algorithm dispatches all events.
     *
     * @return void
     * @throws Exception
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

        $graph = $this->createGraphNodeMock(true);
        $graphSearch = $this->getGraphSearch();
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
     * Tests the method for detecting a cycle in a graph.
     *
     * @return void
     * @throws Exception
     */
    public function testContainsCycle(): void
    {
        $menuWithoutCycle = $this->createGraphNodeMock();
        $menuWithCycle = $this->createGraphNodeMock(true);
        $graphSearch = $this->getGraphSearch();

        $this->assertSame(false, $graphSearch->containsCycle($menuWithoutCycle));
        $this->assertSame(true, $graphSearch->containsCycle($menuWithCycle));
    }

    /**
     * Tests that descendents can be looked up using string paths.
     *
     * @return void
     * @throws Exception
     */
    public function testGetDescendentByPath(): void
    {
        $menu = $this->createGraphNodeMock();
        $graphSearch = $this->getGraphSearch();

        $itemC = $graphSearch->getDescendentByPath($menu, 'a/b/c');
        $this->assertNotNull($itemC);
        $this->assertSame('c', $itemC->getIdentifier());

        $itemB = $graphSearch->getDescendentByPath($menu, 'a/b');
        $this->assertNotNull($itemB);
        $this->assertSame('b', $itemB->getIdentifier());

        $itemC = $graphSearch->getDescendentByPath($itemB, 'c');
        $this->assertNotNull($itemC);
        $this->assertSame('c', $itemC->getIdentifier());

        $item = $graphSearch->getDescendentByPath($menu, 'a/y');
        $this->assertSame(null, $item);
    }

    /**
     * Tests that all child nodes in a menu type tree can be sorted recursively using their priority attribute.
     *
     * @return void
     * @throws Exception
     */
    public function testSortRecursively(): void
    {
        $menuType = $this->createSortableGraphNodeMock();
        $search = $this->getGraphSearch();
        $button1 = $menuType->getChild('button1');

        $this->assertSame(['button1', 'button2'], $this->getGraphNodeChildrenIdentifiers($menuType));
        $this->assertSame(['button3', 'button4'], $this->getGraphNodeChildrenIdentifiers($button1));

        $search->sortChildrenRecursively($menuType);
        $this->assertSame(['button2', 'button1'], $this->getGraphNodeChildrenIdentifiers($menuType));
        $this->assertSame(['button4', 'button3'], $this->getGraphNodeChildrenIdentifiers($button1));
    }

    /**
     * Returns an instance of the graph search service from the service container.
     *
     * @return GraphSearch
     * @throws Exception
     */
    private function getGraphSearch(): GraphSearch
    {
        $container = static::getContainer();

        /** @var GraphSearch $graphSearch */
        $graphSearch = $container->get(GraphSearch::class);
        return $graphSearch;
    }

    /**
     * Creates a graph node tree structure.
     *
     * @param bool $withCycle
     * @return \App\Tests\DataStructure\GraphNodeMock
     */
    private function createGraphNodeMock(bool $withCycle = false): GraphNodeMock
    {
        $root = new GraphNodeMock('root');
        $itemA = new GraphNodeMock('a');
        $itemB = new GraphNodeMock('b');
        $itemC = new GraphNodeMock('c');
        $itemX = new GraphNodeMock('x');
        $itemY = new GraphNodeMock('y');

        $root
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

        return $root;
    }

    /**
     * Creates a graph node tree structure with priorities.
     *
     * @return SortableGraphNodeMock
     */
    private function createSortableGraphNodeMock(): SortableGraphNodeMock
    {
        $root = new SortableGraphNodeMock('root');
        $button1 = new SortableGraphNodeMock('button1');
        $button1->setPriority(1);
        $button2 = new SortableGraphNodeMock('button2');
        $button2->setPriority(2);

        $button3 = new SortableGraphNodeMock('button3');
        $button3->setPriority(1);
        $button4 = new SortableGraphNodeMock('button4');
        $button4->setPriority(2);

        $root
            ->addChild($button1)
            ->addChild($button2)
        ;

        $button1
            ->addChild($button3)
            ->addChild($button4)
        ;

        return $root;
    }
}