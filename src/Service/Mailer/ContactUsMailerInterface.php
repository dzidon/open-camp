<?php

namespace App\Service\Mailer;

/**
 * Sends an email to the camp organizer when someone submits the "contact us" form.
 */
interface ContactUsMailerInterface
{
    /**
     * @param string $name
     * @param string $emailFrom
     * @param string $phoneNumber
     * @param string $message
     * @return void
     */
    public function sendEmail(string $name, string $emailFrom, string $phoneNumber, string $message): void;
}