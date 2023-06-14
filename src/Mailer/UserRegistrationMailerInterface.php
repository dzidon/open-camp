<?php

namespace App\Mailer;

use DateTimeImmutable;

/**
 * Sends emails that are used to finish user registration.
 */
interface UserRegistrationMailerInterface
{
    /**
     * Sends an email with a user registration url.
     *
     * @param string $emailTo
     * @param string $token
     * @param DateTimeImmutable $expireAt
     * @param bool $fake If true, the email is only created and not sent.
     * @return void
     */
    public function sendEmail(string $emailTo, string $token, DateTimeImmutable $expireAt, bool $fake): void;
}