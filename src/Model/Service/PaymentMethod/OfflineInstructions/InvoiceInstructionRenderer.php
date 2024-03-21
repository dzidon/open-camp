<?php

namespace App\Model\Service\PaymentMethod\OfflineInstructions;

use App\Model\Entity\Application;
use App\Model\Service\Application\ApplicationInvoiceNumberFormatterInterface;
use Twig\Environment;

/**
 * Returns HTML instructions for applications that have the invoice payment method assigned.
 */
class InvoiceInstructionRenderer extends AbstractOfflineInstructionRenderer
{
    private ApplicationInvoiceNumberFormatterInterface $invoiceNumberFormatter;

    public function __construct(Environment $twig, ApplicationInvoiceNumberFormatterInterface $invoiceNumberFormatter)
    {
        parent::__construct($twig);

        $this->invoiceNumberFormatter = $invoiceNumberFormatter;
    }

    /**
     * @inheritDoc
     */
    public function supports(Application $application): bool
    {
        $paymentMethod = $application->getPaymentMethod();

        return $paymentMethod !== null && $paymentMethod->getName() === 'invoice';
    }

    /**
     * @inheritDoc
     */
    public function getOfflineInstructionHtml(Application $application, array $options = []): string
    {
        $invoiceNumberFormatted = $this->invoiceNumberFormatter->getFormattedInvoiceNumber($application);
        $template = $this->twig->load('_fragment/_payment_instruction/_invoice.html.twig');

        return $template->render([
            'application'    => $application,
            'invoice_number' => $invoiceNumberFormatted,
        ]);
    }
}