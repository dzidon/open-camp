<?php

namespace App\Service\Twig;

use App\Model\Service\Application\ApplicationInvoiceNumberFormatterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Adds invoice number formatting to Twig.
 */
class InvoiceNumberExtension extends AbstractExtension
{
    private ApplicationInvoiceNumberFormatterInterface $invoiceNumberFormatter;

    public function __construct(ApplicationInvoiceNumberFormatterInterface $invoiceNumberFormatter)
    {
        $this->invoiceNumberFormatter = $invoiceNumberFormatter;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('formatted_invoice_number', [$this->invoiceNumberFormatter, 'getFormattedInvoiceNumber']),
        ];
    }
}