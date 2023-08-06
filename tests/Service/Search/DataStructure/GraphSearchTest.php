<?php

namespace App\Tests\Service\Search\DataStructure;

use App\Library\Event\Search\DepthFirstSearch\ChildIterationEndEvent;
use App\Library\Event\Search\DepthFirstSearch\ChildIterationStartEvent;
use App\Library\Event\Search\DepthFirstSearch\CycleFoundEvent;
use App\Library\Event\Search\DepthFirstSearch\FinishEvent;
use App\Library\Event\Search\DepthFirstSearch\InitialPushEvent;
use App\Library\Event\Search\DepthFirstSearch\NodeMarkAsExpandedEvent;
use App\Library\Event\Search\DepthFirstSearch\StackIterationEndEvent;
use App\Library\Event\Search\DepthFirstSearch\StackPopEvent;
use App\Service\Search\DataStructure\GraphSearch;
use App\Tests\Library\DataStructure\GraphNodeChildrenIdentifiersTrait;
use App\Tests\Library\DataStructure\GraphNodeMock;
use App\Tests\Library\DataStructure\SortableGraphNodeMock;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class GraphSearchTest extends KernelTestCase
{
    use GraphNodeChildrenIdentifiersTrait;

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

    public function testContainsCycle(): void
    {
        $menuWithoutCycle = $this->createGraphNodeMock();
        $menuWithCycle = $this->createGraphNodeMock(true);
        $graphSearch = $this->getGraphSearch();

        $this->assertSame(false, $graphSearch->containsCycle($menuWithoutCycle));
        $this->assertSame(true, $graphSearch->containsCycle($menuWithCycle));
    }

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

    public function testGetDescendentsOfNode(): void
    {
        $menu = $this->createGraphNodeMock();
        $graphSearch = $this->getGraphSearch();

        $descendentIdentifiers = [];
        $descendents = $graphSearch->getDescendentsOfNode($menu);

        /** @var GraphNodeMock $descendent */
        foreach ($descendents as $descendent)
        {
            $descendentIdentifiers[] = $descendent->getIdentifier();
        }

        $this->assertSame(['a', 'b', 'c', 'x', 'y'], $descendentIdentifiers);
    }

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

    private function getGraphSearch(): GraphSearch
    {
        $container = static::getContainer();

        /** @var GraphSearch $graphSearch */
        $graphSearch = $container->get(GraphSearch::class);

        return $graphSearch;
    }

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