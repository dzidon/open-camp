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
     * Returns true if the specified page is out of bounds.
     *
     * @param int $page
     * @return bool
     */
    public function isPageOutOfBounds(int $page): bool;

    /**
     * Returns true if the current page is out of bounds.
     *
     * @return bool
     */
    public function isCurrentPageOutOfBounds(): bool;
}