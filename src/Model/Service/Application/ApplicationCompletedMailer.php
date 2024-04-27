<?php

namespace App\Model\Service\Application;

use App\Model\Entity\Application;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @inheritDoc
 */
class ApplicationCompletedMailer implements ApplicationCompletedMailerInterface
{
    private MailerInterface $mailer;

    private ApplicationInvoiceFilesystemInterface $applicationInvoiceFilesystem;

    private UrlGeneratorInterface $urlGenerator;

    private TranslatorInterface $translator;

    private string $emailFrom;

    public function __construct(MailerInterface                       $mailer,
                                ApplicationInvoiceFilesystemInterface $applicationInvoiceFilesystem,
                                UrlGeneratorInterface                 $urlGenerator,
                                TranslatorInterface                   $translator,
                                string                                $emailFrom)
    {
        $this->mailer = $mailer;
        $this->applicationInvoiceFilesystem = $applicationInvoiceFilesystem;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->emailFrom = $emailFrom;
    }

    /**
     * @inheritDoc
     */
    public function sendEmail(Application $application): void
    {
        $campName = $application->getCampName();
        $applicationId = $application->getId();

        $applicationUrl = $this->urlGenerator->generate('user_application_completed', [
            'applicationId' => $applicationId,
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $subject = $this->translator->trans('mail.application_completed.subject', [
            'camp' => $campName,
        ]);

        $emailTo = $application->getEmail();

        $email = (new TemplatedEmail())
            ->from($this->emailFrom)
            ->to($emailTo)
            ->subject($subject)
            ->htmlTemplate('_fragment/_email/_application_completed.html.twig')
            ->context([
                'application_url' => $applicationUrl,
                'application'     => $application,
            ])
        ;

        $invoiceAttachmentContents = $this->applicationInvoiceFilesystem->getInvoiceContents($application);

        if ($invoiceAttachmentContents !== null)
        {
            $invoiceFileName = $this->translator->trans('mail.application_completed.invoice_attachment_name');
            $mimeTypeDetector = new FinfoMimeTypeDetector();
            $mimeType = $mimeTypeDetector->detectMimeTypeFromBuffer($invoiceAttachmentContents);
            $attachment = new DataPart($invoiceAttachmentContents, $invoiceFileName, $mimeType);
            $email->addPart($attachment);
        }

        $this->mailer->send($email);
    }
}