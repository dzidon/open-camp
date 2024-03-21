<?php

namespace App\Model\Service\Application;

use App\Model\Entity\Application;

/**
 * Formats application invoice numbers.
 */
interface ApplicationInvoiceNumberFormatterInterface
{
    /**
     * Returns a formatted invoice number.
     *
     * @param int|Application $applicationOrInvoiceNumber
     * @return string
     */
    public function getFormattedInvoiceNumber(int|Application $applicationOrInvoiceNumber): string;
}