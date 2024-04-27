<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\PageSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\Page;
use Symfony\Component\Uid\UuidV4;

interface PageRepositoryInterface
{
    /**
     * Saves a page.
     *
     * @param Page $page
     * @param bool $flush
     * @return void
     */
    public function savePage(Page $page, bool $flush): void;

    /**
     * Removes a page.
     *
     * @param Page $page
     * @param bool $flush
     * @return void
     */
    public function removePage(Page $page, bool $flush): void;

    /**
     * Finds all available pages.
     *
     * @return Page[]
     */
    public function findAll(): array;

    /**
     * Finds one page by id.
     *
     * @param UuidV4 $id
     * @return Page|null
     */
    public function findOneById(UuidV4 $id): ?Page;

    /**
     * Finds one page by url name.
     *
     * @param string $urlName
     * @return Page|null
     */
    public function findOneByUrlName(string $urlName): ?Page;

    /**
     * Returns admin page search paginator.
     *
     * @param PageSearchData $data
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(PageSearchData $data, int $currentPage, int $pageSize): PaginatorInterface;
}