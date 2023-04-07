<?php

namespace App\Search\DataStructure;

use App\DataStructure\SortableGraphNodeInterface;
use App\DataStructure\Stack;
use App\DataStructure\GraphNodeInterface;
use App\EventDispatcher\Event\DepthFirstSearch\ChildIterationEndEvent;
use App\EventDispatcher\Event\DepthFirstSearch\ChildIterationStartEvent;
use App\EventDispatcher\Event\DepthFirstSearch\CycleFoundEvent;
use App\EventDispatcher\Event\DepthFirstSearch\FinishEvent;
use App\EventDispatcher\Event\DepthFirstSearch\InitialPushEvent;
use App\EventDispatcher\Event\DepthFirstSearch\NodeMarkAsExpandedEvent;
use App\EventDispatcher\Event\DepthFirstSearch\StackIterationEndEvent;
use App\EventDispatcher\Event\DepthFirstSearch\StackPopEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @inheritDoc
 */
class GraphSearch implements GraphSearchInterface
{
    /**
     * @inheritDoc
     */
    public function depthFirstSearch(GraphNodeInterface $start, EventDispatcherInterface $eventDispatcher): void
    {
        $stack = new Stack();
        $stack->push($start);
        $eventDispatcher->dispatch(new InitialPushEvent($stack), InitialPushEvent::NAME);

        $expandedNodes = [];

        while (!$stack->isEmpty())
        {
            /** @var GraphNodeInterface $currentNode */
            $currentNode = $stack->pop();
            $eventDispatcher->dispatch(
                new StackPopEvent($stack, $expandedNodes, $currentNode),
                StackPopEvent::NAME
            );

            $expandedNodes[] = $currentNode;
            $eventDispatcher->dispatch(
                new NodeMarkAsExpandedEvent($stack, $expandedNodes, $currentNode),
                NodeMarkAsExpandedEvent::NAME
            );

            $childNodes = $currentNode->getChildren();
            for (end($childNodes); key($childNodes) !== null; prev($childNodes))
            {
                $childNode = current($childNodes);
                foreach ($expandedNodes as $expandedNode)
                {
                    if ($childNode->getIdentifier() === $expandedNode->getIdentifier())
                    {
                        $eventDispatcher->dispatch(
                            new CycleFoundEvent($stack, $expandedNodes, $childNode, $currentNode),
                            CycleFoundEvent::NAME
                        );

                        continue 2;
                    }
                }

                $eventDispatcher->dispatch(
                    new ChildIterationStartEvent($stack, $expandedNodes, $childNode, $currentNode),
                    ChildIterationStartEvent::NAME
                );

                $stack->push($childNode);
                $eventDispatcher->dispatch(
                    new ChildIterationEndEvent($stack, $expandedNodes, $childNode, $currentNode),
                    ChildIterationEndEvent::NAME
                );
            }

            $eventDispatcher->dispatch(
                new StackIterationEndEvent($stack, $expandedNodes, $currentNode),
                StackIterationEndEvent::NAME
            );
        }

        $eventDispatcher->dispatch(new FinishEvent($stack, $expandedNodes), FinishEvent::NAME);
    }

    /**
     * @inheritDoc
     */
    public function containsCycle(GraphNodeInterface $start): bool
    {
        $found = false;
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(CycleFoundEvent::NAME, function () use (&$found)
        {
            $found = true;
        });

        $this->depthFirstSearch($start, $dispatcher);
        return $found;
    }

    /**
     * @inheritDoc
     */
    public function getDescendentByPath(GraphNodeInterface $from, string $path): ?GraphNodeInterface
    {
        $names = explode('/', trim($path, '/'));
        if (empty($names))
        {
            return null;
        }

        $currentNode = $from;

        while (true)
        {
            if (empty($names) || $currentNode === null)
            {
                return $currentNode;
            }

            $currentKey = array_key_first($names);
            $currentName = $names[$currentKey];
            $foundChild = false;

            foreach ($currentNode->getChildren() as $child)
            {
                if ($child->getIdentifier() === $currentName)
                {
                    $currentNode = $child;
                    $foundChild = true;
                    break;
                }
            }

            if (!$foundChild)
            {
                $currentNode = null;
            }

            unset($names[$currentKey]);
        }
    }

    /**
     * @inheritDoc
     */
    public function sortChildrenRecursively(SortableGraphNodeInterface $start): void
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(StackPopEvent::NAME, function (StackPopEvent $event)
        {
            $currentNode = $event->getCurrentNode();
            if ($currentNode instanceof SortableGraphNodeInterface)
            {
                $currentNode->sortChildren();
            }
        });

        $this->depthFirstSearch($start, $dispatcher);
    }
}