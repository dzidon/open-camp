<?php

namespace App\Model\Service\PaymentMethod\OfflineInstructions;

use App\Model\Entity\Application;
use Twig\Environment;

/**
 * Returns HTML instructions for applications that have the invoice payment method assigned.
 */
class InvoiceInstructionRenderer extends AbstractOfflineInstructionRenderer
{
    private string $bankAccount;

    private int $invoiceNumberLength;

    public function __construct(Environment $twig, string $bankAccount, int $invoiceNumberLength)
    {
        parent::__construct($twig);

        $this->bankAccount = $bankAccount;
        $this->invoiceNumberLength = $invoiceNumberLength;
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
        $invoiceNumberString = (string) $application->getInvoiceNumber();
        $invoiceNumberFormatted = str_pad($invoiceNumberString, $this->invoiceNumberLength, "0", STR_PAD_LEFT);

        $template = $this->twig->load('_fragment/_payment_instruction/_invoice.html.twig');

        return $template->render([
            'application'    => $application,
            'bank_account'   => $this->bankAccount,
            'invoice_number' => $invoiceNumberFormatted,
        ]);
    }
}