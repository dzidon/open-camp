<?php

namespace App\Tests\Service\Menu\Renderer;

use App\Library\Menu\MenuType;
use App\Service\Menu\Renderer\MenuTypeRenderer;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests that the menu type renderer can convert a menu type to HTML.
 */
class MenuTypeRendererTest extends KernelTestCase
{
    /**
     * Tests the renderer using the menu theme from 'templates/_fragment/_menu_theme/_test.html.twig'.
     *
     * @return void
     * @throws Exception
     */
    public function testRenderMenuType(): void
    {
        $renderer = $this->getMenuTypeRenderer();
        $menuType = $this->createMenuType();
        $menuTypeHtml = $renderer->renderMenuType($menuType);

        $this->assertSame($this->htmlToOneLine('
            <ul>
                <li>
                    <a href="url1">
                        Item 1
                    </a>
                    <ul>
                        <li>
                            <a href="url3">
                                Nested item 1
                            </a>
                        </li>
                        <li>
                            <a href="url4" class="active">
                                Nested item 2
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="url2">
                        Item 2
                    </a>
                </li>
            </ul>'),
        $this->htmlToOneLine($menuTypeHtml));
    }

    /**
     * Removes unnecessary spaces from a string.
     *
     * @param string $html
     * @return string
     */
    private function htmlToOneLine(string $html): string
    {
        return trim(preg_replace('/\s\s+/', '', $html));
    }

    /**
     * Gets an instance of the menu type renderer from the service container.
     *
     * @return MenuTypeRenderer
     * @throws Exception
     */
    private function getMenuTypeRenderer(): MenuTypeRenderer
    {
        $container = static::getContainer();

        /** @var MenuTypeRenderer $renderer */
        $renderer = $container->get(MenuTypeRenderer::class);

        return $renderer;
    }

    /**
     * Creates a menu with nested items.
     *
     * @return MenuType
     */
    private function createMenuType(): MenuType
    {
        $root = new MenuType('test_menu', 'test_root');

        $item1 = new MenuType('item1', 'test_item', 'Item 1', 'url1');
        $item1->setParent($root);

        $item2 = new MenuType('item2', 'test_item', 'Item 2', 'url2');
        $item2->setParent($root);

        $itemNested1 = new MenuType('item_nested_1', 'test_item', 'Nested item 1', 'url3');
        $itemNested1->setParent($item1);

        $itemNested2 = new MenuType('item_nested_2', 'test_item', 'Nested item 2', 'url4');
        $itemNested2->setParent($item1);
        $itemNested2->setActive();

        return $root;
    }
}