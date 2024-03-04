<?php

namespace App\Service\Mailer;

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
    private MailerInterface $mailer;

    private UrlGeneratorInterface $urlGenerator;

    private TranslatorInterface $translator;

    private string $emailFrom;

    public function __construct(MailerInterface $mailer,
                                UrlGeneratorInterface $urlGenerator,
                                TranslatorInterface $translator,
                                string $emailFrom)
    {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;

        $this->emailFrom = $emailFrom;
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

        $email = (new TemplatedEmail())
            ->from($this->emailFrom)
            ->to(new Address($emailTo))
            ->subject($subject)
            ->htmlTemplate('_fragment/_email/_user_registration.html.twig')
            ->context([
                'completion_url' => $completionUrl,
                'expire_at'      => $expireAt,
            ])
        ;

        if (!$fake)
        {
            $this->mailer->send($email);
        }
    }
}