<?php

namespace App\Model\Service\UserPasswordChange;

use DateTimeImmutable;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @inheritDoc
 */
class UserPasswordChangeMailer implements UserPasswordChangeMailerInterface
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
        $completionUrl = $this->urlGenerator->generate('user_password_change_complete', [
            'token' => $token,
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $subject = $this->translator->trans('mail.user_password_change.subject');

        $email = (new TemplatedEmail())
            ->from($this->emailFrom)
            ->to(new Address($emailTo))
            ->subject($subject)
            ->htmlTemplate('_fragment/_email/_user_password_change.html.twig')
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