<?php

namespace App\Service\Search\DataStructure;

use App\Library\DataStructure\TreeNodeInterface;
use App\Library\DataStructure\SortableTreeNodeInterface;
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
class TreeSearch implements TreeSearchInterface
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @inheritDoc
     */
    public function depthFirstSearch(TreeNodeInterface $start, EventDispatcherInterface $eventDispatcher): void
    {
        $stack = new Stack();
        $stack->push($start);
        $eventDispatcher->dispatch(new InitialPushEvent($stack), InitialPushEvent::NAME);

        $expandedNodes = [];

        while (!$stack->isEmpty())
        {
            /** @var TreeNodeInterface $currentNode */
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
    public function containsCycle(TreeNodeInterface $start): bool
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
    public function getDescendentByPath(TreeNodeInterface $from, string $path, string $property = 'identifier'): ?TreeNodeInterface
    {
        $path = trim($path, '/');
        if ($path === '')
        {
            return $from;
        }

        $pathParts = explode('/', $path);
        $currentNode = $from;

        while (true)
        {
            if (empty($pathParts) || $currentNode === null)
            {
                return $currentNode;
            }

            $currentKey = array_key_first($pathParts);
            $currentName = $pathParts[$currentKey];
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

            unset($pathParts[$currentKey]);
        }
    }

    /**
     * @inheritDoc
     */
    public function getDescendentsOfNode(TreeNodeInterface $node): array
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
    public function sortChildrenRecursively(SortableTreeNodeInterface $start): void
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(StackPopEvent::NAME, function (StackPopEvent $event)
        {
            $currentNode = $event->getCurrentNode();
            if ($currentNode instanceof SortableTreeNodeInterface)
            {
                $currentNode->sortChildren();
            }
        });

        $this->depthFirstSearch($start, $dispatcher);
    }
}