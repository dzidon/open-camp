<?php

namespace App\Tests\Functional\Search\Menu;

use App\Menu\Type\MenuType;
use App\Menu\Type\MenuTypeInterface;
use App\Search\DataStructure\GraphSearch;
use App\Search\Menu\MenuSearch;
use PHPUnit\Framework\TestCase;

/**
 * Tests the menu search class.
 */
class MenuSearchTest extends TestCase
{
    /**
     * Tests that all child nodes in a menu type tree can be sorted using their priority attribute.
     *
     * @return void
     */
    public function testSortRecursively(): void
    {
        $menuType = $this->createMenuType();
        $search = $this->createMenuSearch();
        $button1 = $menuType->getChild('button1');

        $this->assertSame(['button1', 'button2'], $this->getMenuTypeChildrenIdentifiers($menuType));
        $this->assertSame(['button3', 'button4'], $this->getMenuTypeChildrenIdentifiers($button1));

        $search->sortChildrenRecursively($menuType);
        $this->assertSame(['button2', 'button1'], $this->getMenuTypeChildrenIdentifiers($menuType));
        $this->assertSame(['button4', 'button3'], $this->getMenuTypeChildrenIdentifiers($button1));
    }

    /**
     * Instantiates the search class.
     *
     * @return MenuSearch
     */
    private function createMenuSearch(): MenuSearch
    {
        return new MenuSearch(new GraphSearch());
    }

    /**
     * Creates a menu type tree structure.
     *
     * @return MenuType
     */
    private function createMenuType(): MenuType
    {
        $menuType = new MenuType('root', 'block_name');
        $button1 = new MenuType('button1', 'block_name');
        $button1->setPriority(1);
        $button2 = new MenuType('button2', 'block_name');
        $button2->setPriority(2);

        $button3 = new MenuType('button3', 'block_name');
        $button3->setPriority(1);
        $button4 = new MenuType('button4', 'block_name');
        $button4->setPriority(2);

        $menuType
            ->addChild($button1)
            ->addChild($button2)
        ;

        $button1
            ->addChild($button3)
            ->addChild($button4)
        ;

        return $menuType;
    }

    /**
     * Returns an array containing identifiers of children of a given menu type.
     *
     * @param MenuTypeInterface $menuType
     * @return array
     */
    private function getMenuTypeChildrenIdentifiers(MenuTypeInterface $menuType): array
    {
        $identifiers = [];
        foreach ($menuType->getChildren() as $child)
        {
            $identifiers[] = $child->getIdentifier();
        }

        return $identifiers;
    }
}