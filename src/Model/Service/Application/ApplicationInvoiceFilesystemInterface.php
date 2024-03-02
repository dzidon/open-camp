<?php

namespace App\Model\Service\Application;

use App\Model\Entity\Application;

/**
 * Helper service for application invoice files.
 */
interface ApplicationInvoiceFilesystemInterface
{
    /**
     * Returns the contents of application's invoice.
     *
     * @param Application $application
     * @return string|null
     */
    public function getInvoiceContents(Application $application): ?string;

    /**
     * Creates an invoice for the given application.
     *
     * @param Application $application
     */
    public function createInvoiceFile(Application $application): void;

    /**
     * Removes the invoice for the given application.
     *
     * @param Application $application
     * @return void
     */
    public function removeInvoiceFile(Application $application): void;
}