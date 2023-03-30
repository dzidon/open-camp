<?php

namespace App\Search\Paginator;

/**
 * Interface for all Paginator classes.
 */
interface PaginatorInterface
{
    /**
     * Returns total number of all items.
     *
     * @return int
     */
    public function getTotalItems(): int;

    /**
     * Returns number of items on the current page.
     *
     * @return int
     */
    public function getPageSize(): int;

    /**
     * Returns items shown on the current page.
     *
     * @return array
     */
    public function getCurrentPageItems(): array;

    /**
     * Returns current page number.
     *
     * @return int
     */
    public function getCurrentPage(): int;

    /**
     * Returns total number of pages.
     *
     * @return int
     */
    public function getPagesCount(): int;

    /**
     * Returns true if the specified page is greater than the total number of pages and is not equal to 1
     * OR if the specified page is lower than 1.
     *
     * @param int $page
     * @return bool
     */
    public function isPageOutOfBounds(int $page): bool;

    /**
     * Returns true if the current page is greater than the total number of pages and is not equal to 1
     * OR if the current page is lower than 1.
     *
     * @return bool
     */
    public function isCurrentPageOutOfBounds(): bool;
}