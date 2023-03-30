<?php

namespace App\Search\Paginator;

use App\Menu\Type\MenuTypeInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface for all classes that build a pagination menu.
 */
interface PaginatorMenuFactoryInterface
{
    /**
     * Creates a pagination menu.
     *
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param array $useAttributes Specifies request attributes which should be added to page URLs.
     * @param string $pageParameterName GET parameter which holds a page number.
     * @param string $templateBlockRoot
     * @param string $templateBlockItem
     * @return MenuTypeInterface
     */
    public function buildMenu(PaginatorInterface $paginator,
                              Request $request,
                              array $useAttributes,
                              string $pageParameterName,
                              string $templateBlockRoot,
                              string $templateBlockItem): MenuTypeInterface;
}