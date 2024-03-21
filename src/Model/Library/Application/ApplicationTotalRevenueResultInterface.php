<?php

namespace App\Model\Library\Application;

/**
 * Contains total revenue grouped by currency.
 */
interface ApplicationTotalRevenueResultInterface
{
    /**
     * Returns a string that shows formatted total revenue in each available currency.
     *
     * @return string
     */
    public function toString(): string;
}