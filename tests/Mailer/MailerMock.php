<?php

namespace App\Tests\Mailer;

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\RawMessage;

/**
 * Mailer mock used for testing.
 */
class MailerMock implements MailerInterface
{
    private array $emailsSent = [];

    /**
     * Adds an email to the array that can be returned using the "getEmailsSent" method.
     *
     * @param RawMessage $message
     * @param Envelope|null $envelope
     * @return void
     */
    public function send(RawMessage $message, Envelope $envelope = null): void
    {
        $this->emailsSent[] = $message;
    }

    /**
     * Returns an array of emails that have been passed to the "send" method.
     *
     * @return array
     */
    public function getEmailsSent(): array
    {
        return $this->emailsSent;
    }
}