<?php

namespace App\Tests\Functional\Menu\Type;

use App\Menu\Type\MenuType;
use App\Tests\Functional\Menu\MenuTypeChildrenIdentifiersTrait;
use PHPUnit\Framework\TestCase;

/**
 * Tests the main menu type implementation.
 */
class MenuTypeTest extends TestCase
{
    use MenuTypeChildrenIdentifiersTrait;

    /**
     * Tests setting and getting the identifier.
     *
     * @return void
     */
    public function testIdentifier(): void
    {
        $menuType = $this->createMenuType(false, false);
        $this->assertSame('button', $menuType->getIdentifier());
    }

    /**
     * Tests setting and getting the text.
     *
     * @return void
     */
    public function testText(): void
    {
        $menuType = $this->createMenuType(false, false);
        $this->assertSame(null, $menuType->getText());

        $menuType->setText('Click here');
        $this->assertSame('Click here', $menuType->getText());

        $menuType->setText(null);
        $this->assertSame(null, $menuType->getText());
    }

    /**
     * Tests setting and getting the url.
     *
     * @return void
     */
    public function testUrl(): void
    {
        $menuType = $this->createMenuType(false, false);
        $this->assertSame('#', $menuType->getUrl());

        $menuType->setUrl('url/url');
        $this->assertSame('url/url', $menuType->getUrl());
    }

    /**
     * Tests the "active" flag.
     *
     * @return void
     */
    public function testActive(): void
    {
        $menuType = $this->createMenuType(false, false);
        $this->assertSame(false, $menuType->isActive());

        $menuType->setActive();
        $this->assertSame(true, $menuType->isActive());

        $menuType->setActive(false);
        $this->assertSame(false, $menuType->isActive());

        $menuType = $this->createMenuType(true, true);
        $child = $menuType->getChild('child_button1');
        $child->setActive();
        $this->assertSame(true, $child->isActive());
        $this->assertSame(true, $menuType->isActive());
        $this->assertSame(true, $menuType->getParent()->isActive());

        $child->setActive(false);
        $this->assertSame(false, $child->isActive());
        $this->assertSame(false, $menuType->isActive());
        $this->assertSame(false, $menuType->getParent()->isActive());

        $child->setActive(true, false);
        $this->assertSame(true, $child->isActive());
        $this->assertSame(false, $menuType->isActive());
        $this->assertSame(false, $menuType->getParent()->isActive());
    }

    /**
     * Tests setting and getting the priority.
     *
     * @return void
     */
    public function testPriority(): void
    {
        $menuType = $this->createMenuType(false, false);
        $this->assertSame(0, $menuType->getPriority());

        $menuType->setPriority(1);
        $this->assertSame(1, $menuType->getPriority());
    }

    /**
     * Tests setting and getting the parent menu type. Also tests the relationship consistency.
     *
     * @return void
     */
    public function testParent(): void
    {
        $menuType = $this->createMenuType(false, false);
        $this->assertSame(null, $menuType->getParent());

        $menuType = $this->createMenuType(true, false);
        $parent = $menuType->getParent();
        $this->assertSame('parent_button', $parent->getIdentifier());
        $this->assertSame($menuType, $parent->getChild('button'));

        $menuType->setParent(null);
        $this->assertSame(null, $menuType->getParent());
        $this->assertSame(null, $parent->getChild('button'));
        $this->assertSame(false, $parent->hasChild('button'));
    }

    /**
     * Tests adding and removing a child menu type. Also tests the relationship consistency.
     *
     * @return void
     */
    public function testChildren(): void
    {
        $menuType = $this->createMenuType(false, false);
        $this->assertSame(0, count($menuType->getChildren()));

        $menuType = $this->createMenuType(false, true);
        $this->assertSame(2, count($menuType->getChildren()));
        $this->assertSame(true, $menuType->hasChild('child_button1'));
        $this->assertSame(true, $menuType->hasChild('child_button2'));

        $child = $menuType->getChild('child_button1');
        $this->assertSame('child_button1', $child->getIdentifier());
        $this->assertSame($menuType, $child->getParent());

        $menuType->removeChild($child);
        $this->assertSame(null, $menuType->getChild('child_button1'));
        $this->assertSame(false, $menuType->hasChild('child_button1'));
        $this->assertSame(null, $child->getParent());

        $menuType = $this->createMenuType(false, true);
        $child = $menuType->getChild('child_button1');
        $menuType->removeChild('child_button1');
        $this->assertSame(null, $menuType->getChild('child_button1'));
        $this->assertSame(false, $menuType->hasChild('child_button1'));
        $this->assertSame(null, $child->getParent());
    }

    /**
     * Tests that child nodes can be rewritten safely.
     *
     * @return void
     */
    public function testChildRewriting(): void
    {
        $menuType = $this->createMenuType(false, true);
        $oldChildButton1 = $menuType->getChild('child_button1');
        $newChildButton1 = new MenuType('child_button1', 'child_button_block_name');
        $menuType->addChild($newChildButton1);

        $this->assertSame($newChildButton1, $menuType->getChild('child_button1'));
        $this->assertSame($menuType, $newChildButton1->getParent());
        $this->assertSame(null, $oldChildButton1->getParent());
        $this->assertSame(false, in_array($oldChildButton1, $menuType->getChildren(), true));
    }

    /**
     * Tests that child menu types can be sorted using the priority attribute.
     *
     * @return void
     */
    public function testSortChildren(): void
    {
        $menuType = $this->createMenuType(false, true);
        $this->assertSame(['child_button1', 'child_button2'], $this->getMenuTypeChildrenIdentifiers($menuType));

        $button1 = $menuType->getChild('child_button1');
        $button1->setPriority(1);
        $button2 = $menuType->getChild('child_button2');
        $button2->setPriority(2);
        $menuType->sortChildren();
        $this->assertSame(['child_button2', 'child_button1'], $this->getMenuTypeChildrenIdentifiers($menuType));
    }

    /**
     * Tests setting and getting the template block name.
     *
     * @return void
     */
    public function testTemplateBlock(): void
    {
        $menuType = $this->createMenuType(false, false);
        $this->assertSame('button_block_name', $menuType->getTemplateBlock());

        $menuType->setTemplateBlock('button_block_name_new');
        $this->assertSame('button_block_name_new', $menuType->getTemplateBlock());
    }

    /**
     * Creates a menu type.
     *
     * @param bool $withParent
     * @param bool $withChildren
     * @return MenuType
     */
    private function createMenuType(bool $withParent, bool $withChildren): MenuType
    {
        $menuType = new MenuType('button', 'button_block_name');
        if ($withParent)
        {
            $parent = new MenuType('parent_button', 'parent_button_block_name');
            $menuType->setParent($parent);
        }

        if ($withChildren)
        {
            $menuType->addChild(new MenuType('child_button1', 'child_button_block_name'));
            $menuType->addChild(new MenuType('child_button2', 'child_button_block_name'));
        }

        return $menuType;
    }
}