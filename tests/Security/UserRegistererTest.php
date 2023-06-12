<?php

namespace App\Tests\Security;

use App\Enum\Entity\UserRegistrationStateEnum;
use App\Repository\UserRegistrationRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Security\UserRegisterer;
use App\Tests\Mailer\MailerMock;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;

/**
 * Tests creating, verifying and completing user registrations.
 */
class UserRegistererTest extends KernelTestCase
{
    public function testCreateUserRegistration(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();
        $mailer = $this->getMailerMock();

        $registerer->createUserRegistration('bob@gmail.com');
        $activeRegistrations = $registrationRepository->findByEmail('bob@gmail.com', true);

        $this->assertCount(1, $activeRegistrations);
        $this->assertCount(1, $mailer->getEmailsSent());
    }

    public function testCreateUserRegistrationIfOneExists(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();
        $mailer = $this->getMailerMock();

        $registerer->createUserRegistration('max@gmail.com');
        $activeRegistrations = $registrationRepository->findByEmail('max@gmail.com', true);

        $this->assertCount(2, $activeRegistrations);
        $this->assertCount(1, $mailer->getEmailsSent());
    }

    public function testCreateUserRegistrationIfEmailIsRegistered(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();
        $mailer = $this->getMailerMock();

        $registerer->createUserRegistration('david@gmail.com');
        $activeRegistrations = $registrationRepository->findByEmail('david@gmail.com', true);

        $this->assertCount(0, $activeRegistrations);
        $this->assertCount(0, $mailer->getEmailsSent());
    }

    public function testCreateUserRegistrationIfActiveAmountExceeded(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();
        $mailer = $this->getMailerMock();

        $registerer->createUserRegistration('roman@gmail.com');
        $activeRegistrations = $registrationRepository->findByEmail('roman@gmail.com', true);

        $this->assertCount(2, $activeRegistrations);
        $this->assertCount(0, $mailer->getEmailsSent());
    }

    public function testCreateUserRegistrationIfOneIsActiveAndOneIsTimeExpired(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();
        $mailer = $this->getMailerMock();

        $registerer->createUserRegistration('lucas@gmail.com');
        $activeRegistrations = $registrationRepository->findByEmail('lucas@gmail.com', true);

        $this->assertCount(2, $activeRegistrations);
        $this->assertCount(1, $mailer->getEmailsSent());
    }

    public function testCreateUserRegistrationIfOneIsActiveAndOneIsDisabled(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();
        $mailer = $this->getMailerMock();

        $registerer->createUserRegistration('tim@gmail.com');
        $activeRegistrations = $registrationRepository->findByEmail('tim@gmail.com', true);

        $this->assertCount(2, $activeRegistrations);
        $this->assertCount(1, $mailer->getEmailsSent());
    }

    public function testCreateUserRegistrationIfOneIsActiveAndOneIsUsed(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();
        $mailer = $this->getMailerMock();

        $registerer->createUserRegistration('alena@gmail.com');
        $activeRegistrations = $registrationRepository->findByEmail('alena@gmail.com', true);

        $this->assertCount(2, $activeRegistrations);
        $this->assertCount(1, $mailer->getEmailsSent());
    }

    public function testVerify(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();
        $registration = $registrationRepository->findOneBySelector('max');

        $this->assertTrue($registerer->verify($registration, '123'));
        $this->assertFalse($registerer->verify($registration, '321'));
    }

    public function testCompleteUserRegistration(): void
    {
        $registerer = $this->getUserRegisterer();
        $userRepository = $this->getUserRepository();
        $registrationRepository = $this->getUserRegistrationRepository();
        $registration = $registrationRepository->findOneBySelector('max');

        $registerer->completeUserRegistration($registration, '123456');
        $this->assertTrue($userRepository->isEmailRegistered('max@gmail.com'));

        $registration = $registrationRepository->findOneBySelector('max');
        $this->assertSame(UserRegistrationStateEnum::USED->value, $registration->getState());
    }

    public function testCompleteUserRegistrationIfMultipleActiveExistForEmail(): void
    {
        $registerer = $this->getUserRegisterer();
        $userRepository = $this->getUserRepository();
        $registrationRepository = $this->getUserRegistrationRepository();
        $registration1 = $registrationRepository->findOneBySelector('ro1');

        $registerer->completeUserRegistration($registration1, '123456');
        $this->assertTrue($userRepository->isEmailRegistered('roman@gmail.com'));

        $registration1 = $registrationRepository->findOneBySelector('ro1');
        $registration2 = $registrationRepository->findOneBySelector('ro2');
        $this->assertSame(UserRegistrationStateEnum::USED->value, $registration1->getState());
        $this->assertSame(UserRegistrationStateEnum::DISABLED->value, $registration2->getState());
    }

    public function testCompleteUserRegistrationIfOneIsTimeExpired(): void
    {
        $registerer = $this->getUserRegisterer();
        $userRepository = $this->getUserRepository();
        $registrationRepository = $this->getUserRegistrationRepository();
        $registration1 = $registrationRepository->findOneBySelector('lu1');

        $registerer->completeUserRegistration($registration1, '123456');
        $this->assertTrue($userRepository->isEmailRegistered('lucas@gmail.com'));

        $registration1 = $registrationRepository->findOneBySelector('lu1');
        $registration2 = $registrationRepository->findOneBySelector('lu2');
        $this->assertSame(UserRegistrationStateEnum::USED->value, $registration1->getState());
        $this->assertSame(UserRegistrationStateEnum::UNUSED->value, $registration2->getState());
    }

    public function testCompleteUserRegistrationIfOneIsDisabled(): void
    {
        $registerer = $this->getUserRegisterer();
        $userRepository = $this->getUserRepository();
        $registrationRepository = $this->getUserRegistrationRepository();
        $registration1 = $registrationRepository->findOneBySelector('ti1');

        $registerer->completeUserRegistration($registration1, '123456');
        $this->assertTrue($userRepository->isEmailRegistered('tim@gmail.com'));

        $registration1 = $registrationRepository->findOneBySelector('ti1');
        $registration2 = $registrationRepository->findOneBySelector('ti2');
        $this->assertSame(UserRegistrationStateEnum::USED->value, $registration1->getState());
        $this->assertSame(UserRegistrationStateEnum::DISABLED->value, $registration2->getState());
    }

    public function testCompleteUserRegistrationIfEmailIsRegistered(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();
        $registration1 = $registrationRepository->findOneBySelector('ka1');

        $registerer->completeUserRegistration($registration1, '123456');

        $registration1 = $registrationRepository->findOneBySelector('ka1');
        $registration2 = $registrationRepository->findOneBySelector('ka2');
        $this->assertSame(UserRegistrationStateEnum::DISABLED->value, $registration1->getState());
        $this->assertSame(UserRegistrationStateEnum::DISABLED->value, $registration2->getState());
    }

    private function getUserRegistrationRepository(): UserRegistrationRepositoryInterface
    {
        $container = static::getContainer();

        /** @var UserRegistrationRepositoryInterface $repository */
        $repository = $container->get(UserRegistrationRepositoryInterface::class);

        return $repository;
    }

    private function getUserRepository(): UserRepositoryInterface
    {
        $container = static::getContainer();

        /** @var UserRepositoryInterface $repository */
        $repository = $container->get(UserRepositoryInterface::class);

        return $repository;
    }

    private function getUserRegisterer(): UserRegisterer
    {
        $container = static::getContainer();

        /** @var UserRegisterer $registerer */
        $registerer = $container->get(UserRegisterer::class);

        return $registerer;
    }

    private function getMailerMock(): MailerMock
    {
        $container = static::getContainer();

        /** @var MailerMock $mailer Configured in services_test.yaml */
        $mailer = $container->get(MailerInterface::class);

        return $mailer;
    }
}