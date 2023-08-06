<?php

namespace App\Service\Menu\Factory;

use App\Library\Menu\MenuType;
use App\Library\Search\Paginator\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Creates a basic pagination menu.
 */
class PaginatorMenuTypeFactory extends AbstractMenuTypeFactory
{
    private const VIEW_INNER_PAGES = 5;
    private const VIEW_INNER_CENTER = 3;

    private UrlGeneratorInterface $urlGenerator;
    private RequestStack $requestStack;

    public function __construct(UrlGeneratorInterface $urlGenerator, RequestStack $requestStack)
    {
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
    }

    /**
     * @inheritDoc
     */
    public static function getMenuIdentifier(): string
    {
        return 'pagination';
    }

    /**
     * @inheritDoc
     */
    public function buildMenuType(array $options = []): MenuType
    {
        /** @var PaginatorInterface $paginator */
        $paginator = $options['paginator'];
        $pageParameterName = $options['page_parameter_name'];
        $templateBlockRoot = $options['template_block_root'];
        $templateBlockItem = $options['template_block_item'];

        $menu = new MenuType(self::getMenuIdentifier(), $templateBlockRoot);
        $request = $this->requestStack->getCurrentRequest();
        if ($request === null)
        {
            return $menu;
        }

        // extract data from the paginator
        $currentPage = $paginator->getCurrentPage();
        $pagesCount = $paginator->getPagesCount();

        // extract data from the request
        $requestParameters = $request->query;
        $requestAttributes = $request->attributes;
        $requestParametersAll = $requestParameters->all();
        $route = $requestAttributes->get('_route');
        $routeParams = $requestAttributes->get('_route_params', []);

        $queryParameters = array_merge($requestParametersAll, $routeParams);

        // distance calculations used for building the menu
        $maxSlotsSurroundingCenter = self::VIEW_INNER_PAGES - self::VIEW_INNER_CENTER; // 2
        $slotsLeftToCenter = $currentPage - 1;
        if ($slotsLeftToCenter > $maxSlotsSurroundingCenter)
        {
            $slotsLeftToCenter = $maxSlotsSurroundingCenter;
        }

        $slotsRightToCenter = $pagesCount - $currentPage;
        if ($slotsRightToCenter > $maxSlotsSurroundingCenter)
        {
            $slotsRightToCenter = $maxSlotsSurroundingCenter;
        }

        $directions = [
            'left' => [
                'offset' => -1,
                'next' => 'right',
                'allowedDistance' => $slotsLeftToCenter + ($maxSlotsSurroundingCenter - $slotsRightToCenter),
            ],
            'right' => [
                'offset' => 1,
                'next' => 'stop',
                'allowedDistance' => $slotsRightToCenter + ($maxSlotsSurroundingCenter - $slotsLeftToCenter),
            ],
            'stop' => [
                'offset' => 0,
            ],
        ];

        $direction = $directions['left'];
        $visitedPage = $currentPage;
        $lowestPage = null;
        $highestPage = null;

        while (count($menu->getChildren()) < self::VIEW_INNER_PAGES && $direction !== $directions['stop'])
        {
            $this->addMenuPage($menu, $visitedPage === $currentPage, $templateBlockItem, $visitedPage, $pageParameterName, $route, $queryParameters);

            // update lowest and highest page
            if ($lowestPage === null || $visitedPage < $lowestPage)
            {
                $lowestPage = $visitedPage;
            }

            if ($highestPage === null || $visitedPage > $highestPage)
            {
                $highestPage = $visitedPage;
            }

            // update iterator
            $nextPage = $visitedPage + $direction['offset'];
            if ($paginator->isPageOutOfBounds($nextPage) ||
                abs($currentPage - $visitedPage) >= $direction['allowedDistance'])
            {
                $direction = $directions[$direction['next']];
                $visitedPage = $currentPage;
            }
            else
            {
                $visitedPage = $nextPage;
            }
        }

        // first page & (second page / divider)
        if ($lowestPage !== null)
        {
            $difference = $lowestPage - 1;
            if ($difference >= 1)
            {
                $this->addMenuPage($menu, false, $templateBlockItem, 1, $pageParameterName, $route, $queryParameters);
            }

            if ($difference == 2)
            {
                $this->addMenuPage($menu, false, $templateBlockItem, 2, $pageParameterName, $route, $queryParameters);
            }
            else if ($difference > 2)
            {
                $this->addMenuDivider($menu, $templateBlockItem, 2);
            }
        }

        // last page & (page before / divider)
        if ($highestPage !== null)
        {
            $difference = $pagesCount - $highestPage;
            if ($difference >= 1)
            {
                $this->addMenuPage($menu, false, $templateBlockItem, $pagesCount, $pageParameterName, $route, $queryParameters);
            }

            if ($difference == 2)
            {
                $this->addMenuPage($menu, false, $templateBlockItem, $pagesCount - 1, $pageParameterName, $route, $queryParameters);
            }
            else if ($difference > 2)
            {
                $this->addMenuDivider($menu, $templateBlockItem, $pagesCount - 1);
            }
        }

        $menu->sortChildren();

        return $menu;
    }

    /**
     * Configures the following options: paginator, page_parameter_name, template_block_root, and template_block_item.
     *
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['paginator', 'page_parameter_name', 'template_block_root', 'template_block_item']);

        $resolver->setDefaults([
            'page_parameter_name' => 'page',
            'template_block_root' => 'pagination_root',
            'template_block_item' => 'pagination_item',
        ]);

        $resolver->setAllowedTypes('paginator', PaginatorInterface::class);
        $resolver->setAllowedTypes('page_parameter_name', 'string');
        $resolver->setAllowedTypes('template_block_root', 'string');
        $resolver->setAllowedTypes('template_block_item', 'string');
    }

    /**
     * Adds a page button to the specified menu.
     *
     * @param MenuType $menu
     * @param bool $active
     * @param string $templateBlockItem
     * @param int $page
     * @param string $pageParameterName
     * @param string $route
     * @param array $queryParameters
     * @return void
     */
    private function addMenuPage(MenuType $menu,
                                 bool $active,
                                 string $templateBlockItem,
                                 int $page,
                                 string $pageParameterName,
                                 string $route,
                                 array $queryParameters = []): void
    {
        $pageId = sprintf('page_%s', $page);
        if (!$menu->hasChild($pageId))
        {
            $queryParameters[$pageParameterName] = $page;
            $url = $this->urlGenerator->generate($route, $queryParameters);

            $pageButton = new MenuType($pageId, $templateBlockItem, (string) $page, $url);
            $pageButton
                ->setActive($active)
                ->setPriority(-$page)
            ;

            $menu->addChild($pageButton);
        }
    }

    /**
     * Adds a divider to the specified menu.
     *
     * @param MenuType $menu
     * @param string $templateBlockItem
     * @param int $asPage
     * @return void
     */
    private function addMenuDivider(MenuType $menu, string $templateBlockItem, int $asPage): void
    {
        $dividerId = sprintf('divider_%s', $asPage);
        if (!$menu->hasChild($dividerId))
        {
            $divider = new MenuType($dividerId, $templateBlockItem, '...', '#');
            $divider->setPriority(-$asPage);

            $menu->addChild($divider);
        }
    }
}