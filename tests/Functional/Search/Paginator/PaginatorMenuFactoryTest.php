<?php

namespace App\Tests\Functional\Search\Paginator;

use App\Search\Paginator\PaginatorInterface;
use App\Search\Paginator\PaginatorMenuFactory;
use App\Tests\Functional\DataStructure\GraphNodeChildrenIdentifiersTrait;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tests that the paginator menu factory correctly creates pagination menus.
 */
class PaginatorMenuFactoryTest extends KernelTestCase
{
    use GraphNodeChildrenIdentifiersTrait;

    private const ROUTE = 'admin_profile';
    private const PAGE_PARAM_NAME = 'page';
    private const TEMPLATE_BLOCK_ROOT = 'pagination_root';
    private const TEMPLATE_BLOCK_ITEM = 'pagination_item';

    /**
     * Tests basic getters of the root menu type (getIdentifier & getTemplateBlock).
     *
     * @return void
     * @throws Exception
     */
    public function testRoot(): void
    {
        $paginator = $this->createPaginator(0, 1);
        $menuFactory = $this->getPaginatorMenuFactory();
        $request = $this->createRequest(['_route' => self::ROUTE]);
        $menu = $menuFactory->buildMenu($paginator, $request, [], self::PAGE_PARAM_NAME, self::TEMPLATE_BLOCK_ROOT, self::TEMPLATE_BLOCK_ITEM);

        $this->assertSame('pagination', $menu->getIdentifier());
        $this->assertSame(self::TEMPLATE_BLOCK_ROOT, $menu->getTemplateBlock());
    }

    /**
     * Tests basic getters of the child menu type (getIdentifier & getTemplateBlock).
     *
     * @return void
     * @throws Exception
     */
    public function testChild(): void
    {
        $paginator = $this->createPaginator(1, 1);
        $menuFactory = $this->getPaginatorMenuFactory();
        $request = $this->createRequest(['_route' => self::ROUTE]);
        $menu = $menuFactory->buildMenu($paginator, $request, [], self::PAGE_PARAM_NAME, self::TEMPLATE_BLOCK_ROOT, self::TEMPLATE_BLOCK_ITEM);

        $itemPage1 = $menu->getChild('page_1');
        $this->assertNotNull($itemPage1);

        $this->assertSame('page_1', $itemPage1->getIdentifier());
        $this->assertSame(self::TEMPLATE_BLOCK_ITEM, $itemPage1->getTemplateBlock());
    }

    /**
     * Tests that the 'active' flag is set correctly based on the current page number.
     *
     * @return void
     * @throws Exception
     */
    public function testActive(): void
    {
        $paginator = $this->createPaginator(3, 2);
        $menuFactory = $this->getPaginatorMenuFactory();
        $request = $this->createRequest(['_route' => self::ROUTE]);
        $menu = $menuFactory->buildMenu($paginator, $request, [], self::PAGE_PARAM_NAME, self::TEMPLATE_BLOCK_ROOT, self::TEMPLATE_BLOCK_ITEM);

        $itemPage1 = $menu->getChild('page_1');
        $this->assertNotNull($itemPage1);
        $this->assertSame(false, $itemPage1->isActive());

        $itemPage2 = $menu->getChild('page_2');
        $this->assertNotNull($itemPage2);
        $this->assertSame(true, $itemPage2->isActive());

        $itemPage3 = $menu->getChild('page_3');
        $this->assertNotNull($itemPage3);
        $this->assertSame(false, $itemPage3->isActive());
    }

    /**
     * Tests that the page link menu types are sorted in ascending order.
     *
     * @return void
     * @throws Exception
     */
    public function testOrder(): void
    {
        $paginator = $this->createPaginator(5, 5);
        $menuFactory = $this->getPaginatorMenuFactory();
        $request = $this->createRequest(['_route' => self::ROUTE]);
        $menu = $menuFactory->buildMenu($paginator, $request, [], self::PAGE_PARAM_NAME, self::TEMPLATE_BLOCK_ROOT, self::TEMPLATE_BLOCK_ITEM);
        $identifiers = $this->getGraphNodeChildrenIdentifiers($menu);

        $this->assertSame(['page_1', 'page_2', 'page_3', 'page_4', 'page_5'], $identifiers);
    }

    /**
     * Tests that each page link menu type has the correct URL.
     *
     * @return void
     * @throws Exception
     */
    public function testUrls(): void
    {
        // without parameters
        $paginator = $this->createPaginator(2, 1);
        $menuFactory = $this->getPaginatorMenuFactory();
        $request = $this->createRequest(['_route' => self::ROUTE]);
        $menu = $menuFactory->buildMenu($paginator, $request, [], self::PAGE_PARAM_NAME, self::TEMPLATE_BLOCK_ROOT, self::TEMPLATE_BLOCK_ITEM);

        $itemPage1 = $menu->getChild('page_1');
        $itemPage2 = $menu->getChild('page_2');
        $this->assertNotNull($itemPage1);
        $this->assertNotNull($itemPage2);

        $this->assertSame('/admin/profile?page=1', $itemPage1->getUrl());
        $this->assertSame('/admin/profile?page=2', $itemPage2->getUrl());

        // with query parameters
        $request = $this->createRequest(['_route' => self::ROUTE, 'abc' => '123', 'xyz' => '456'], ['aaa' => 'bbb', 'ccc' => 'ddd']);
        $menu = $menuFactory->buildMenu($paginator, $request, ['abc', 'xyz'], self::PAGE_PARAM_NAME, self::TEMPLATE_BLOCK_ROOT, self::TEMPLATE_BLOCK_ITEM);

        $itemPage1 = $menu->getChild('page_1');
        $itemPage2 = $menu->getChild('page_2');
        $this->assertNotNull($itemPage1);
        $this->assertNotNull($itemPage2);

        $this->assertSame('/admin/profile?aaa=bbb&ccc=ddd&abc=123&xyz=456&page=1', $itemPage1->getUrl());
        $this->assertSame('/admin/profile?aaa=bbb&ccc=ddd&abc=123&xyz=456&page=2', $itemPage2->getUrl());
    }

    /**
     * Tests all possible shapes of the pagination menu.
     *
     * @return void
     * @throws Exception
     */
    public function testAllMenuShapes(): void
    {
        $menuFactory = $this->getPaginatorMenuFactory();
        $request = $this->createRequest(['_route' => self::ROUTE]);

        // Small
        $expectedSequences = [
            1 => ['page_1'],
            2 => ['page_1', 'page_2'],
            3 => ['page_1', 'page_2', 'page_3'],
            4 => ['page_1', 'page_2', 'page_3', 'page_4'],
            5 => ['page_1', 'page_2', 'page_3', 'page_4', 'page_5'],
            6 => ['page_1', 'page_2', 'page_3', 'page_4', 'page_5', 'page_6'],
            7 => ['page_1', 'page_2', 'page_3', 'page_4', 'page_5', 'page_6', 'page_7'],
        ];

        foreach ($expectedSequences as $pagesCount => $expectedSequence)
        {
            // first page is active
            $paginator = $this->createPaginator($pagesCount, 1);
            $menu = $menuFactory->buildMenu($paginator, $request, [], self::PAGE_PARAM_NAME, self::TEMPLATE_BLOCK_ROOT, self::TEMPLATE_BLOCK_ITEM);
            $identifiers = $this->getGraphNodeChildrenIdentifiers($menu);

            $this->assertSame($expectedSequence, $identifiers);

            // last page is active
            $paginator = $this->createPaginator($pagesCount, array_key_last($expectedSequence) + 1);
            $menu = $menuFactory->buildMenu($paginator, $request, [], self::PAGE_PARAM_NAME, self::TEMPLATE_BLOCK_ROOT, self::TEMPLATE_BLOCK_ITEM);
            $identifiers = $this->getGraphNodeChildrenIdentifiers($menu);

            $this->assertSame($expectedSequence, $identifiers);
        }

        // Large
        $expectedSequences = [
            1 => ['page_1', 'page_2', 'page_3', 'page_4', 'page_5', 'divider_10', 'page_11'],
            2 => ['page_1', 'page_2', 'page_3', 'page_4', 'page_5', 'divider_10', 'page_11'],
            3 => ['page_1', 'page_2', 'page_3', 'page_4', 'page_5', 'divider_10', 'page_11'],
            4 => ['page_1', 'page_2', 'page_3', 'page_4', 'page_5', 'page_6', 'divider_10', 'page_11'],
            5 => ['page_1', 'page_2', 'page_3', 'page_4', 'page_5', 'page_6', 'page_7', 'divider_10', 'page_11'],
            6 => ['page_1', 'divider_2', 'page_4', 'page_5', 'page_6', 'page_7', 'page_8', 'divider_10', 'page_11'],
            7 => ['page_1', 'divider_2', 'page_5', 'page_6', 'page_7', 'page_8', 'page_9', 'page_10', 'page_11'],
            8 => ['page_1', 'divider_2', 'page_6', 'page_7', 'page_8', 'page_9', 'page_10', 'page_11'],
            9 => ['page_1', 'divider_2', 'page_7', 'page_8', 'page_9', 'page_10', 'page_11'],
            10 => ['page_1', 'divider_2', 'page_7', 'page_8', 'page_9', 'page_10', 'page_11'],
            11 => ['page_1', 'divider_2', 'page_7', 'page_8', 'page_9', 'page_10', 'page_11'],
        ];

        foreach ($expectedSequences as $currentPage => $expectedSequence)
        {
            $paginator = $this->createPaginator(11, $currentPage);
            $menu = $menuFactory->buildMenu($paginator, $request, [], self::PAGE_PARAM_NAME, self::TEMPLATE_BLOCK_ROOT, self::TEMPLATE_BLOCK_ITEM);
            $identifiers = $this->getGraphNodeChildrenIdentifiers($menu);

            $this->assertSame($expectedSequence, $identifiers);
        }

        // Extra use case:
        // Tests that pages 2 and 8 are added to the menu if current page is 5 (center) and total number of pages is 9.
        $paginator = $this->createPaginator(9, 5);
        $menu = $menuFactory->buildMenu($paginator, $request, [], self::PAGE_PARAM_NAME, self::TEMPLATE_BLOCK_ROOT, self::TEMPLATE_BLOCK_ITEM);
        $identifiers = $this->getGraphNodeChildrenIdentifiers($menu);

        $this->assertSame(['page_1', 'page_2', 'page_3', 'page_4', 'page_5', 'page_6', 'page_7', 'page_8', 'page_9'], $identifiers);
    }

    /**
     * Gets an instance of the paginator menu factory from the service container.
     *
     * @return PaginatorMenuFactory
     * @throws Exception
     */
    private function getPaginatorMenuFactory(): PaginatorMenuFactory
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var PaginatorMenuFactory $menuRegistry */
        $menuRegistry = $container->get(PaginatorMenuFactory::class);
        return $menuRegistry;
    }

    /**
     * Creates an instance of a request.
     *
     * @param array $queryParameters
     * @param array $attributes
     * @return Request
     */
    private function createRequest(array $attributes = [], array $queryParameters = []): Request
    {
        return new Request($queryParameters, [], $attributes);
    }

    /**
     * Creates an instance of the paginator.
     *
     * @param int $pagesCount
     * @param int $currentPage
     * @return PaginatorInterface
     */
    private function createPaginator(int $pagesCount, int $currentPage): PaginatorInterface
    {
        /** @var PaginatorInterface|MockObject $paginatorMock */
        $paginatorMock = $this->getMockBuilder(PaginatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $paginatorMock
            ->expects($this->any())
            ->method('getCurrentPage')
            ->willReturn($currentPage)
        ;

        $paginatorMock
            ->expects($this->any())
            ->method('getPagesCount')
            ->willReturn($pagesCount)
        ;

        $paginatorMock
            ->expects($this->any())
            ->method('isPageOutOfBounds')
            ->willReturnCallback(function (int $page) use ($pagesCount) {
                return (($page > $pagesCount && $page !== 1) || $page < 1);
            })
        ;

        return $paginatorMock;
    }
}