<?php

namespace App\Tests\Mailer;

use App\Mailer\UserPasswordChangeMailer;
use DateTimeImmutable;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;

/**
 * Tests the class that sends user password change emails.
 */
class UserPasswordChangeMailerTest extends KernelTestCase
{
    /**
     * Tests that the email is sent correctly.
     *
     * @return void
     */
    public function testSendEmail(): void
    {
        $passwordChangeMailer = $this->getUserPasswordChangeMailer();
        $mailerMock = $this->getMailerMock();
        $expireAt = new DateTimeImmutable('3000-01-01 12:00:00');

        $passwordChangeMailer->sendEmail('to@email.com', 'abc', $expireAt, false);
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
        $this->assertSame('mail.user_password_change.subject', $email->getSubject());
        $this->assertSame('_fragment/_email/_user_password_change.html.twig', $email->getHtmlTemplate());

        $context = $email->getContext();
        $this->assertSame('mail.user_password_change.body1', $context['body1']);
        $this->assertSame('mail.user_password_change.link_text', $context['link_text']);
        $this->assertSame('mail.user_password_change.body2', $context['body2']);
        $this->assertStringContainsString('abc', $context['completion_url']);
    }

    /**
     * Tests that no email is sent if "fake" is set to true.
     *
     * @return void
     */
    public function testSendEmailFake(): void
    {
        $passwordChangeMailer = $this->getUserPasswordChangeMailer();
        $mailerMock = $this->getMailerMock();
        $expireAt = new DateTimeImmutable('3000-01-01 12:00:00');

        $passwordChangeMailer->sendEmail('to@email.com', 'abc', $expireAt, true);
        $emailsSent = $mailerMock->getEmailsSent();

        $this->assertEmpty($emailsSent);
    }

    private function getUserPasswordChangeMailer(): UserPasswordChangeMailer
    {
        $container = static::getContainer();

        /** @var UserPasswordChangeMailer $mailer */
        $mailer = $container->get(UserPasswordChangeMailer::class);

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