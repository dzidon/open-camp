<?php

namespace App\Service\Mailer;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactUsMailer implements ContactUsMailerInterface
{
    private MailerInterface $mailer;

    private TranslatorInterface $translator;

    private string $emailTo;

    public function __construct(
        MailerInterface     $mailer,
        TranslatorInterface $translator,

        #[Autowire('%app.email%')]
        string $emailTo
    ) {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->emailTo = $emailTo;
    }

    /**
     * @inheritDoc
     */
    public function sendEmail(string $name, string $emailFrom, string $phoneNumber, string $message): void
    {
        $subject = $this->translator->trans('mail.user_contact_message.subject', [
            'sender' => $name,
        ]);

        $email = (new TemplatedEmail())
            ->from($emailFrom)
            ->to(new Address($this->emailTo))
            ->subject($subject)
            ->htmlTemplate('_fragment/_email/_user_contact_message.html.twig')
            ->context([
                'sender_name'         => $name,
                'sender_email'        => $emailFrom,
                'sender_phone_number' => $phoneNumber,
                'message'             => $message,
            ])
        ;

        $this->mailer->send($email);
    }
}