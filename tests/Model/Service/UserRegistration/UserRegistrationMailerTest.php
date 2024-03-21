<?php

namespace App\Tests\Model\Service\UserRegistration;

use App\Model\Service\UserRegistration\UserRegistrationMailer;
use App\Tests\Service\Mailer\MailerMock;
use DateTimeImmutable;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;

/**
 * Tests the class that sends user registration emails.
 */
class UserRegistrationMailerTest extends KernelTestCase
{
    /**
     * Tests that the email is sent correctly.
     *
     * @return void
     */
    public function testSendEmail(): void
    {
        $registrationMailer = $this->getUserRegistrationMailer();
        $mailerMock = $this->getMailerMock();
        $expireAt = new DateTimeImmutable('3000-01-01 12:00:00');

        $registrationMailer->sendEmail('to@email.com', 'abc', $expireAt, false);
        $emailsSent = $mailerMock->getEmailsSent();

        $this->assertCount(1, $emailsSent);

        /** @var TemplatedEmail $email */
        $email = $emailsSent[0];
        $from = $email->getFrom();
        $this->assertArrayHasKey(0, $from);

        $to = $email->getTo();
        $this->assertArrayHasKey(0, $to);

        $this->assertSame('noreply@camp.com', $from[0]->getAddress());
        $this->assertSame('to@email.com', $to[0]->getAddress());
        $this->assertSame('mail.user_registration.subject', $email->getSubject());
        $this->assertSame('_fragment/_email/_user_registration.html.twig', $email->getHtmlTemplate());

        $context = $email->getContext();
        $this->assertSame($expireAt, $context['expire_at']);
        $this->assertStringContainsString('abc', $context['completion_url']);
    }

    /**
     * Tests that no email is sent if "fake" is set to true.
     *
     * @return void
     */
    public function testSendEmailFake(): void
    {
        $registrationMailer = $this->getUserRegistrationMailer();
        $mailerMock = $this->getMailerMock();
        $expireAt = new DateTimeImmutable('3000-01-01 12:00:00');

        $registrationMailer->sendEmail('to@email.com', 'abc', $expireAt, true);
        $emailsSent = $mailerMock->getEmailsSent();

        $this->assertEmpty($emailsSent);
    }

    private function getUserRegistrationMailer(): UserRegistrationMailer
    {
        $container = static::getContainer();

        /** @var UserRegistrationMailer $mailer */
        $mailer = $container->get(UserRegistrationMailer::class);

        return $mailer;
    }

    private function getMailerMock(): MailerMock
    {
        $container = static::getContainer();

        /** @var MailerMock $mailer Configured in services_test.yaml */
        $mailer = $container->get(MailerInterface::class);

        return $mailer;
    }
}