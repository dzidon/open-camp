<?php

namespace App\Mailer;

use DateTimeImmutable;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @inheritDoc
 */
class UserRegistrationMailer implements UserRegistrationMailerInterface
{
    private string $emailFrom;
    private string $dateTimeFormat;

    private MailerInterface $mailer;
    private UrlGeneratorInterface $urlGenerator;
    private TranslatorInterface $translator;

    public function __construct(MailerInterface $mailer,
                                UrlGeneratorInterface $urlGenerator,
                                TranslatorInterface $translator,
                                string $emailFrom,
                                string $dateTimeFormat)
    {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;

        $this->emailFrom = $emailFrom;
        $this->dateTimeFormat = $dateTimeFormat;
    }

    /**
     * @inheritDoc
     */
    public function sendEmail(string $emailTo, string $token, DateTimeImmutable $expireAt, bool $fake): void
    {
        $completionUrl = $this->urlGenerator->generate('user_registration_complete', [
            'token' => $token,
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $subject = $this->translator->trans('mail.user_registration.subject');
        $body1 = $this->translator->trans('mail.user_registration.body1');
        $linkText = $this->translator->trans('mail.user_registration.link_text');
        $body2 = $this->translator->trans('mail.user_registration.body2', [
            'date' => $expireAt->format($this->dateTimeFormat),
        ]);

        $email = (new TemplatedEmail())
            ->from($this->emailFrom)
            ->to(new Address($emailTo))
            ->subject($subject)
            ->htmlTemplate('_fragment/_email/_user_registration.html.twig')
            ->context([
                'completion_url' => $completionUrl,
                'body1'          => $body1,
                'link_text'      => $linkText,
                'body2'          => $body2,
            ])
        ;

        if (!$fake)
        {
            $this->mailer->send($email);
        }
    }
}