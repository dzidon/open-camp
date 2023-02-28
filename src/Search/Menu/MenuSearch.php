<?php

namespace App\Search\Menu;

use App\EventDispatcher\Event\DepthFirstSearch\StackPopEvent;
use App\Menu\Type\MenuTypeInterface;
use App\Search\DataStructure\GraphSearchInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @inheritDoc
 */
class MenuSearch implements MenuSearchInterface
{
    private GraphSearchInterface $graphSearch;

    public function __construct(GraphSearchInterface $graphSearch)
    {
        $this->graphSearch = $graphSearch;
    }

    /**
     * @inheritDoc
     */
    public function sortChildrenRecursively(MenuTypeInterface $menuType): void
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(StackPopEvent::NAME, function (StackPopEvent $event)
        {
            /** @var MenuTypeInterface $currentNode */
            $currentNode = $event->getCurrentNode();
            $currentNode->sortChildren();
        });

        $this->graphSearch->depthFirstSearch($menuType, $dispatcher);
    }
}