<?php

namespace App\Model\Service\Application;

use App\Model\Entity\Application;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @inheritDoc
 */
class ApplicationStateChangedMailer implements ApplicationStateChangedMailerInterface
{
    private MailerInterface $mailer;

    private UrlGeneratorInterface $urlGenerator;

    private TranslatorInterface $translator;

    private string $emailFrom;

    public function __construct(
        MailerInterface       $mailer,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface   $translator,

        #[Autowire('%app.email_no_reply%')]
        string $emailFrom
    ) {
        $this->mailer = $mailer;
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

        if ($application->isAccepted() === true)
        {
            $subject = $this->translator->trans('mail.application_state_changed.subject.accepted', ['camp' => $campName]);
        }
        else if ($application->isAccepted() === false)
        {
            $subject = $this->translator->trans('mail.application_state_changed.subject.declined', ['camp' => $campName]);
        }
        else
        {
            $subject = $this->translator->trans('mail.application_state_changed.subject.unsettled', ['camp' => $campName]);
        }

        $emailTo = $application->getEmail();

        $email = (new TemplatedEmail())
            ->from($this->emailFrom)
            ->to(new Address($emailTo))
            ->subject($subject)
            ->htmlTemplate('_fragment/_email/_application_state_changed.html.twig')
            ->context([
                'application_url' => $applicationUrl,
                'application'     => $application,
            ])
        ;

        $this->mailer->send($email);
    }
}