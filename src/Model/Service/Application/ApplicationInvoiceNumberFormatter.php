<?php

namespace App\Model\Service\Application;

use App\Model\Entity\Application;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @inheritDoc
 */
class ApplicationInvoiceNumberFormatter implements ApplicationInvoiceNumberFormatterInterface
{
    private int $invoiceNumberLength;

    public function __construct(
        #[Autowire('%app.application_invoice_number_length%')]
        int $invoiceNumberLength
    ) {
        $this->invoiceNumberLength = $invoiceNumberLength;
    }

    /**
     * @inheritDoc
     */
    public function getFormattedInvoiceNumber(Application|int $applicationOrInvoiceNumber): string
    {
        $invoiceNumber = $applicationOrInvoiceNumber;

        if ($applicationOrInvoiceNumber instanceof Application)
        {
            $invoiceNumber = $applicationOrInvoiceNumber->getInvoiceNumber();
        }

        return str_pad((string) $invoiceNumber, $this->invoiceNumberLength, "0", STR_PAD_LEFT);
    }
}