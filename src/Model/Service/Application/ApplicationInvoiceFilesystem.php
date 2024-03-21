<?php

namespace App\Model\Service\Application;

use App\Model\Entity\Application;
use League\Flysystem\FilesystemOperator;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * @inheritDoc
 */
class ApplicationInvoiceFilesystem implements ApplicationInvoiceFilesystemInterface
{
    private TranslatorInterface $translator;

    private FilesystemOperator $applicationInvoiceStorage;

    private ApplicationInvoiceNumberFormatterInterface $invoiceNumberFormatter;

    private Environment $twig;

    private string $publicFilePathPrefix;

    public function __construct(TranslatorInterface                        $translator,
                                FilesystemOperator                         $applicationInvoiceStorage,
                                ApplicationInvoiceNumberFormatterInterface $invoiceNumberFormatter,
                                Environment                                $twig,
                                string                                     $publicFilePathPrefix)
    {
        $this->translator = $translator;
        $this->applicationInvoiceStorage = $applicationInvoiceStorage;
        $this->invoiceNumberFormatter = $invoiceNumberFormatter;
        $this->twig = $twig;
        $this->publicFilePathPrefix = $publicFilePathPrefix;
    }

    /**
     * @inheritDoc
     */
    public function getInvoiceContents(Application $application): ?string
    {
        $fileName = $this->getApplicationInvoiceFileName($application);

        if (!$this->applicationInvoiceStorage->has($fileName))
        {
            return null;
        }

        return $this->applicationInvoiceStorage->read($fileName);
    }

    /**
     * @inheritDoc
     */
    public function createInvoiceFile(Application $application): void
    {
        $newFileName = $this->getApplicationInvoiceFileName($application);
        $pdf = new Mpdf(['mode' => 'utf-8']);
        $pdf->curlAllowUnsafeSslRequests = true;
        $pdf->SetBasePath($this->publicFilePathPrefix);

        $invoiceTitle = $this->translator->trans('application.invoice.title');
        $pageNumberText = $this->translator->trans('application.invoice.page_number');
        $pdf->setFooter("|$pageNumberText|");

        $invoiceNumberFormatted = $this->invoiceNumberFormatter->getFormattedInvoiceNumber($application);

        $html = $this->twig->render('_fragment\_application\_invoice.html.twig', [
            'application'    => $application,
            'invoice_number' => $invoiceNumberFormatted
        ]);

        $pdf->WriteHTML($html);
        $contents = $pdf->Output($invoiceTitle, Destination::STRING_RETURN);
        $this->applicationInvoiceStorage->write($newFileName, $contents);
    }

    /**
     * @inheritDoc
     */
    public function removeInvoiceFile(Application $application): void
    {
        $fileName = $this->getApplicationInvoiceFileName($application);

        if (!$this->applicationInvoiceStorage->has($fileName))
        {
            return;
        }

        $this->applicationInvoiceStorage->delete($fileName);
    }

    private function getApplicationInvoiceFileName(Application $application): string
    {
        $applicationId = $application->getId();

        return $applicationId->toRfc4122() . '.pdf';
    }
}