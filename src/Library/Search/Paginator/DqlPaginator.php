<?php

namespace App\Library\Search\Paginator;

use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

/**
 * Class for paginating Doctrine queries. Calculates offset & limit, applies it to the query and fetches results.
 */
class DqlPaginator implements PaginatorInterface
{
    private int $pageSize;
    private int $currentPage;
    private int $pagesCount;
    private array $currentPageItems;
    private int $totalItems;

    public function __construct(DoctrinePaginator $paginator, int $currentPage, int $pageSize)
    {
        $this->pageSize = $pageSize;
        $this->currentPage = $currentPage;
        if ($this->currentPage < 1)
        {
            $this->currentPage = 1;
        }

        // paginate
        $this->totalItems = count($paginator);
        $this->pagesCount = (int) ceil($this->totalItems / $this->pageSize);

        $this->currentPageItems = $paginator->getQuery()
            ->setFirstResult($this->pageSize * ($this->currentPage - 1))
            ->setMaxResults($this->pageSize)
            ->getResult()
        ;
    }

    /**
     * @inheritDoc
     */
    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    /**
     * @inheritDoc
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentPageItems(): array
    {
        return $this->currentPageItems;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @inheritDoc
     */
    public function getPagesCount(): int
    {
        return $this->pagesCount;
    }

    /**
     * @inheritDoc
     */
    public function isPageOutOfBounds(int $page): bool
    {
        return (($page > $this->pagesCount && $page !== 1) || $page < 1);
    }

    /**
     * @inheritDoc
     */
    public function isCurrentPageOutOfBounds(): bool
    {
        return $this->isPageOutOfBounds($this->currentPage);
    }
}