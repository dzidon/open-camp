<?php

namespace App\Tests\Functional\Search\DataStructure;

use App\Menu\Type\MenuType;
use App\Search\DataStructure\TreeSearch;
use PHPUnit\Framework\TestCase;

/**
 * Tests the tree search class.
 */
class TreeSearchTest extends TestCase
{
    /**
     * Tests that descendents can be looked up using string paths.
     *
     * @return void
     */
    public function testGetDescendentByPath(): void
    {
        $menu = $this->createMenuType();
        $treeSearch = $this->createTreeSearch();

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
     * Creates an instance of the search class.
     *
     * @return TreeSearch
     */
    private function createTreeSearch(): TreeSearch
    {
        return new TreeSearch();
    }

    /**
     * Creates a menu type.
     *
     * @return MenuType
     */
    private function createMenuType(): MenuType
    {
        $menu = new MenuType('menu', 'menu_block');
        $itemA = new MenuType('a', 'item_block');
        $itemB = new MenuType('b', 'item_block');
        $itemC = new MenuType('c', 'item_block');
        $itemX = new MenuType('x', 'item_block');
        $itemY = new MenuType('Y', 'item_block');

        $menu
            ->addChild($itemA)
            ->addChild($itemX)
        ;

        $itemA->addChild($itemB);
        $itemB->addChild($itemC);
        $itemX->addChild($itemY);

        return $menu;
    }
}