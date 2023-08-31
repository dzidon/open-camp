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
use App\Service\Search\DataStructure\TreeSearch;
use App\Tests\Library\DataStructure\TreeNodeChildrenIdentifiersTrait;
use App\Tests\Library\DataStructure\TreeNodeMock;
use App\Tests\Library\DataStructure\SortableTreeNodeMock;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class TreeSearchTest extends KernelTestCase
{
    use TreeNodeChildrenIdentifiersTrait;

    public function testDepthFirstSearchOrder(): void
    {
        $tree = $this->createTreeNodeMock();
        $treeSearch = $this->getTreeSearch();
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

        $tree = $this->createTreeNodeMock(true);
        $treeSearch = $this->getTreeSearch();
        $dispatcher = new EventDispatcher();

        foreach ($eventsDispatched as $eventName => $dispatched)
        {
            $dispatcher->addListener($eventName, function () use (&$eventsDispatched, $eventName)
            {
                $eventsDispatched[$eventName] = true;
            });
        }

        $treeSearch->depthFirstSearch($tree, $dispatcher);
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
        $menuWithoutCycle = $this->createTreeNodeMock();
        $menuWithCycle = $this->createTreeNodeMock(true);
        $treeSearch = $this->getTreeSearch();

        $this->assertSame(false, $treeSearch->containsCycle($menuWithoutCycle));
        $this->assertSame(true, $treeSearch->containsCycle($menuWithCycle));
    }

    public function testGetDescendentByPath(): void
    {
        $menu = $this->createTreeNodeMock();
        $treeSearch = $this->getTreeSearch();

        $itemMenu = $treeSearch->getDescendentByPath($menu, '');
        $this->assertSame($itemMenu, $menu);

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

    public function testGetDescendentsOfNode(): void
    {
        $menu = $this->createTreeNodeMock();
        $treeSearch = $this->getTreeSearch();

        $descendentIdentifiers = [];
        $descendents = $treeSearch->getDescendentsOfNode($menu);

        /** @var TreeNodeMock $descendent */
        foreach ($descendents as $descendent)
        {
            $descendentIdentifiers[] = $descendent->getIdentifier();
        }

        $this->assertSame(['a', 'b', 'c', 'x', 'y'], $descendentIdentifiers);
    }

    public function testSortRecursively(): void
    {
        $menuType = $this->createSortableTreeNodeMock();
        $search = $this->getTreeSearch();
        $button1 = $menuType->getChild('button1');

        $this->assertSame(['button1', 'button2'], $this->getTreeNodeChildrenIdentifiers($menuType));
        $this->assertSame(['button3', 'button4'], $this->getTreeNodeChildrenIdentifiers($button1));

        $search->sortChildrenRecursively($menuType);
        $this->assertSame(['button2', 'button1'], $this->getTreeNodeChildrenIdentifiers($menuType));
        $this->assertSame(['button4', 'button3'], $this->getTreeNodeChildrenIdentifiers($button1));
    }

    private function getTreeSearch(): TreeSearch
    {
        $container = static::getContainer();

        /** @var TreeSearch $treeSearch */
        $treeSearch = $container->get(TreeSearch::class);

        return $treeSearch;
    }

    private function createTreeNodeMock(bool $withCycle = false): TreeNodeMock
    {
        $root = new TreeNodeMock('root');
        $itemA = new TreeNodeMock('a');
        $itemB = new TreeNodeMock('b');
        $itemC = new TreeNodeMock('c');
        $itemX = new TreeNodeMock('x');
        $itemY = new TreeNodeMock('y');

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

    private function createSortableTreeNodeMock(): SortableTreeNodeMock
    {
        $root = new SortableTreeNodeMock('root');
        $button1 = new SortableTreeNodeMock('button1');
        $button1->setPriority(1);
        $button2 = new SortableTreeNodeMock('button2');
        $button2->setPriority(2);

        $button3 = new SortableTreeNodeMock('button3');
        $button3->setPriority(1);
        $button4 = new SortableTreeNodeMock('button4');
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