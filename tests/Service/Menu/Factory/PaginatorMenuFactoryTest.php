<?php

namespace App\Tests\Service\Menu\Factory;

use App\Library\Search\Paginator\PaginatorInterface;
use App\Service\Menu\Factory\PaginatorMenuTypeFactory;
use App\Tests\Library\DataStructure\TreeNodeChildrenIdentifiersTrait;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Tests that the paginator menu factory correctly creates pagination menus.
 */
class PaginatorMenuFactoryTest extends KernelTestCase
{
    use TreeNodeChildrenIdentifiersTrait;

    private const ROUTE = 'route_mock';

    /**
     * Tests that the options are configured properly.
     *
     * @return void
     */
    public function testConfigureOptions(): void
    {
        $request = $this->createRequest();
        $menuFactory = $this->getPaginatorMenuTypeFactory($request);
        $resolver = new OptionsResolver();

        $menuFactory->configureOptions($resolver);
        $required = $resolver->getRequiredOptions();
        $defined = $resolver->getDefinedOptions();

        $this->assertSame($defined, $required);
        $this->assertContains('paginator', $defined);
        $this->assertContains('page_parameter_name', $defined);
        $this->assertContains('template_block_root', $defined);
        $this->assertContains('template_block_item', $defined);
    }

    /**
     * Tests basic getters of the root menu type (getIdentifier & getTemplateBlock).
     *
     * @return void
     * @throws Exception
     */
    public function testRoot(): void
    {
        $paginator = $this->createPaginator(0, 1);
        $request = $this->createRequest(['_route' => self::ROUTE]);
        $menuFactory = $this->getPaginatorMenuTypeFactory($request);
        $menu = $menuFactory->buildMenuType([
            'paginator' => $paginator,
            'page_parameter_name' => 'page',
            'template_block_root' => 'pagination_root',
            'template_block_item' => 'pagination_item',
        ]);

        $this->assertSame('pagination', $menu->getIdentifier());
        $this->assertSame('pagination_root', $menu->getTemplateBlock());
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
        $request = $this->createRequest(['_route' => self::ROUTE]);
        $menuFactory = $this->getPaginatorMenuTypeFactory($request);
        $menu = $menuFactory->buildMenuType([
            'paginator' => $paginator,
            'page_parameter_name' => 'page',
            'template_block_root' => 'pagination_root',
            'template_block_item' => 'pagination_item',
        ]);

        $itemPage1 = $menu->getChild('page_1');
        $this->assertNotNull($itemPage1);

        $this->assertSame('page_1', $itemPage1->getIdentifier());
        $this->assertSame('pagination_item', $itemPage1->getTemplateBlock());
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
        $request = $this->createRequest(['_route' => self::ROUTE]);
        $menuFactory = $this->getPaginatorMenuTypeFactory($request);
        $menu = $menuFactory->buildMenuType([
            'paginator' => $paginator,
            'page_parameter_name' => 'page',
            'template_block_root' => 'pagination_root',
            'template_block_item' => 'pagination_item',
        ]);

        $previousPage = $menu->getChild('previous');
        $this->assertNotNull($previousPage);
        $this->assertSame(false, $previousPage->isActive());

        $itemPage1 = $menu->getChild('page_1');
        $this->assertNotNull($itemPage1);
        $this->assertSame(false, $itemPage1->isActive());

        $itemPage2 = $menu->getChild('page_2');
        $this->assertNotNull($itemPage2);
        $this->assertSame(true, $itemPage2->isActive());

        $itemPage3 = $menu->getChild('page_3');
        $this->assertNotNull($itemPage3);
        $this->assertSame(false, $itemPage3->isActive());

        $nextPage = $menu->getChild('next');
        $this->assertNotNull($nextPage);
        $this->assertSame(false, $nextPage->isActive());
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
        $request = $this->createRequest(['_route' => self::ROUTE]);
        $menuFactory = $this->getPaginatorMenuTypeFactory($request);
        $menu = $menuFactory->buildMenuType([
            'paginator' => $paginator,
            'page_parameter_name' => 'page',
            'template_block_root' => 'pagination_root',
            'template_block_item' => 'pagination_item',
        ]);
        $identifiers = $this->getTreeNodeChildrenIdentifiers($menu);

        $this->assertSame(['previous', 'page_1', 'page_2', 'page_3', 'page_4', 'page_5', 'next'], $identifiers);
    }

    /**
     * Tests that each page link menu type has the correct URL (without parameters).
     *
     * @return void
     * @throws Exception
     */
    public function testUrlsWithoutParameters(): void
    {
        $paginator = $this->createPaginator(2, 1);
        $request = $this->createRequest(['_route' => self::ROUTE]);
        $menuFactory = $this->getPaginatorMenuTypeFactory($request);
        $menu = $menuFactory->buildMenuType([
            'paginator' => $paginator,
            'page_parameter_name' => 'page',
            'template_block_root' => 'pagination_root',
            'template_block_item' => 'pagination_item',
        ]);

        $previousPage = $menu->getChild('previous');
        $itemPage1 = $menu->getChild('page_1');
        $itemPage2 = $menu->getChild('page_2');
        $nextPage = $menu->getChild('next');

        $this->assertNotNull($previousPage);
        $this->assertNotNull($itemPage1);
        $this->assertNotNull($itemPage2);
        $this->assertNotNull($nextPage);

        $this->assertSame('#', $previousPage->getUrl());
        $this->assertSame('/route/mock?page=1', $itemPage1->getUrl());
        $this->assertSame('/route/mock?page=2', $itemPage2->getUrl());
        $this->assertSame('/route/mock?page=2', $nextPage->getUrl());
    }

    /**
     * Tests that each page link menu type has the correct URL (with params).
     *
     * @return void
     * @throws Exception
     */
    public function testUrlsWithParameters(): void
    {
        $paginator = $this->createPaginator(2, 2);
        $request = $this->createRequest(
            ['_route' => self::ROUTE],
            ['abc' => '123', 'xyz' => '456'],
            ['aaa' => 'bbb', 'ccc' => 'ddd']
        );
        $menuFactory = $this->getPaginatorMenuTypeFactory($request);
        $menu = $menuFactory->buildMenuType([
            'paginator' => $paginator,
            'page_parameter_name' => 'page',
            'template_block_root' => 'pagination_root',
            'template_block_item' => 'pagination_item',
        ]);

        $previousPage = $menu->getChild('previous');
        $itemPage1 = $menu->getChild('page_1');
        $itemPage2 = $menu->getChild('page_2');
        $nextPage = $menu->getChild('next');

        $this->assertNotNull($previousPage);
        $this->assertNotNull($itemPage1);
        $this->assertNotNull($itemPage2);
        $this->assertNotNull($nextPage);

        $this->assertSame('/route/mock?aaa=bbb&ccc=ddd&abc=123&xyz=456&page=1', $previousPage->getUrl());
        $this->assertSame('/route/mock?aaa=bbb&ccc=ddd&abc=123&xyz=456&page=1', $itemPage1->getUrl());
        $this->assertSame('/route/mock?aaa=bbb&ccc=ddd&abc=123&xyz=456&page=2', $itemPage2->getUrl());
        $this->assertSame('#', $nextPage->getUrl());
    }

    /**
     * Tests all possible shapes of the pagination menu.
     *
     * @return void
     * @throws Exception
     */
    public function testAllMenuShapes(): void
    {
        $request = $this->createRequest(['_route' => self::ROUTE]);
        $menuFactory = $this->getPaginatorMenuTypeFactory($request);

        // Small
        $expectedSequences = [
            1 => ['previous', 'page_1', 'next'],
            2 => ['previous', 'page_1', 'page_2', 'next'],
            3 => ['previous', 'page_1', 'page_2', 'page_3', 'next'],
            4 => ['previous', 'page_1', 'page_2', 'page_3', 'page_4', 'next'],
            5 => ['previous', 'page_1', 'page_2', 'page_3', 'page_4', 'page_5', 'next'],
            6 => ['previous', 'page_1', 'page_2', 'page_3', 'page_4', 'page_5', 'page_6', 'next'],
            7 => ['previous', 'page_1', 'page_2', 'page_3', 'page_4', 'page_5', 'page_6', 'page_7', 'next'],
        ];

        foreach ($expectedSequences as $pagesCount => $expectedSequence)
        {
            // first page is active
            $paginator = $this->createPaginator($pagesCount, 1);
            $menu = $menuFactory->buildMenuType([
                'paginator'           => $paginator,
                'page_parameter_name' => 'page',
                'template_block_root' => 'pagination_root',
                'template_block_item' => 'pagination_item',
            ]);
            $identifiers = $this->getTreeNodeChildrenIdentifiers($menu);

            $this->assertSame($expectedSequence, $identifiers);

            // last page is active
            $paginator = $this->createPaginator($pagesCount, $pagesCount);
            $menu = $menuFactory->buildMenuType([
                'paginator'           => $paginator,
                'page_parameter_name' => 'page',
                'template_block_root' => 'pagination_root',
                'template_block_item' => 'pagination_item',
            ]);
            $identifiers = $this->getTreeNodeChildrenIdentifiers($menu);

            $this->assertSame($expectedSequence, $identifiers);
        }

        // Large
        $expectedSequences = [
            1 => ['previous', 'page_1', 'page_2', 'page_3', 'page_4', 'page_5', 'divider_10', 'page_11', 'next'],
            2 => ['previous', 'page_1', 'page_2', 'page_3', 'page_4', 'page_5', 'divider_10', 'page_11', 'next'],
            3 => ['previous', 'page_1', 'page_2', 'page_3', 'page_4', 'page_5', 'divider_10', 'page_11', 'next'],
            4 => ['previous', 'page_1', 'page_2', 'page_3', 'page_4', 'page_5', 'page_6', 'divider_10', 'page_11', 'next'],
            5 => ['previous', 'page_1', 'page_2', 'page_3', 'page_4', 'page_5', 'page_6', 'page_7', 'divider_10', 'page_11', 'next'],
            6 => ['previous', 'page_1', 'divider_2', 'page_4', 'page_5', 'page_6', 'page_7', 'page_8', 'divider_10', 'page_11', 'next'],
            7 => ['previous', 'page_1', 'divider_2', 'page_5', 'page_6', 'page_7', 'page_8', 'page_9', 'page_10', 'page_11', 'next'],
            8 => ['previous', 'page_1', 'divider_2', 'page_6', 'page_7', 'page_8', 'page_9', 'page_10', 'page_11', 'next'],
            9 => ['previous', 'page_1', 'divider_2', 'page_7', 'page_8', 'page_9', 'page_10', 'page_11', 'next'],
            10 => ['previous', 'page_1', 'divider_2', 'page_7', 'page_8', 'page_9', 'page_10', 'page_11', 'next'],
            11 => ['previous', 'page_1', 'divider_2', 'page_7', 'page_8', 'page_9', 'page_10', 'page_11', 'next'],
        ];

        foreach ($expectedSequences as $currentPage => $expectedSequence)
        {
            $paginator = $this->createPaginator(11, $currentPage);
            $menu = $menuFactory->buildMenuType([
                'paginator' => $paginator,
                'page_parameter_name' => 'page',
                'template_block_root' => 'pagination_root',
                'template_block_item' => 'pagination_item',
            ]);
            $identifiers = $this->getTreeNodeChildrenIdentifiers($menu);

            $this->assertSame($expectedSequence, $identifiers);
        }

        // Extra use case:
        // Tests that pages 2 and 8 are added to the menu if current page is 5 (center) and total number of pages is 9.
        $paginator = $this->createPaginator(9, 5);
        $menu = $menuFactory->buildMenuType([
            'paginator' => $paginator,
            'page_parameter_name' => 'page',
            'template_block_root' => 'pagination_root',
            'template_block_item' => 'pagination_item',
        ]);
        $identifiers = $this->getTreeNodeChildrenIdentifiers($menu);

        $this->assertSame(['previous', 'page_1', 'page_2', 'page_3', 'page_4', 'page_5', 'page_6', 'page_7', 'page_8', 'page_9', 'next'], $identifiers);
    }

    /**
     * Creates an instance of a request.
     *
     * @param array $attributes
     * @param array $routeParams
     * @param array $queryParameters
     * @return Request
     */
    private function createRequest(array $attributes = [], array $routeParams = [], array $queryParameters = []): Request
    {
        $attributes = array_merge($attributes, $routeParams);
        $attributes['_route_params'] = $routeParams;

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
        $isOutOfBoundsCallable = function (int $page) use ($pagesCount) {
            return (($page > $pagesCount && $page !== 1) || $page < 1);
        };

        $previousPage = $isOutOfBoundsCallable($currentPage - 1) ? null : $currentPage - 1;
        $nextPage = $isOutOfBoundsCallable($currentPage + 1) ? null : $currentPage + 1;

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
            ->method('getPreviousPage')
            ->willReturn($previousPage)
        ;

        $paginatorMock
            ->expects($this->any())
            ->method('getNextPage')
            ->willReturn($nextPage)
        ;

        $paginatorMock
            ->expects($this->any())
            ->method('isPageOutOfBounds')
            ->willReturnCallback($isOutOfBoundsCallable)
        ;

        return $paginatorMock;
    }

    private function getPaginatorMenuTypeFactory(Request $request): PaginatorMenuTypeFactory
    {
        $container = static::getContainer();

        /** @var RequestStack $requestStack */
        $requestStack = $container->get(RequestStack::class);
        $requestStack->push($request);

        /** @var PaginatorMenuTypeFactory $menuRegistry */
        $menuRegistry = $container->get(PaginatorMenuTypeFactory::class);

        return $menuRegistry;
    }
}