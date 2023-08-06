<?php

namespace App\Service\Search\DataStructure;

use App\Library\DataStructure\GraphNodeInterface;
use App\Library\DataStructure\SortableGraphNodeInterface;
use App\Library\DataStructure\Stack;
use App\Library\Event\Search\DepthFirstSearch\ChildIterationEndEvent;
use App\Library\Event\Search\DepthFirstSearch\ChildIterationStartEvent;
use App\Library\Event\Search\DepthFirstSearch\CycleFoundEvent;
use App\Library\Event\Search\DepthFirstSearch\FinishEvent;
use App\Library\Event\Search\DepthFirstSearch\InitialPushEvent;
use App\Library\Event\Search\DepthFirstSearch\NodeMarkAsExpandedEvent;
use App\Library\Event\Search\DepthFirstSearch\StackIterationEndEvent;
use App\Library\Event\Search\DepthFirstSearch\StackPopEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @inheritDoc
 */
class GraphSearch implements GraphSearchInterface
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

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
    public function getDescendentByPath(GraphNodeInterface $from, string $path, string $property = 'identifier'): ?GraphNodeInterface
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
                $value = (string) $this->propertyAccessor->getValue($child, $property);

                if ($value === $currentName)
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
    public function getDescendentsOfNode(GraphNodeInterface $node): array
    {
        $descendents = [];
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(StackPopEvent::NAME, function (StackPopEvent $event) use (&$descendents, $node)
        {
            $descendent = $event->getCurrentNode();

            if ($descendent->getIdentifier() !== $node->getIdentifier())
            {
                $descendents[] = $descendent;
            }
        });

        $this->depthFirstSearch($node, $dispatcher);

        return $descendents;
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