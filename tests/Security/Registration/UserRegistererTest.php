<?php

namespace App\Tests\Security\Registration;

use App\Enum\Entity\UserRegistrationStateEnum;
use App\Repository\UserRegistrationRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Security\Registration\UserRegisterer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Tests user registration completion.
 */
class UserRegistererTest extends KernelTestCase
{
    public function testCompleteUserRegistration(): void
    {
        $plainPassword = '123456';
        $registerer = $this->getUserRegisterer();
        $userRepository = $this->getUserRepository();
        $registrationRepository = $this->getUserRegistrationRepository();
        $userPasswordHasher = $this->getUserPasswordHasher();
        $registration = $registrationRepository->findOneBySelector('max');

        $registerer->completeUserRegistration($registration, $plainPassword, true);

        $user = $userRepository->findOneByEmail('max@gmail.com');
        $this->assertNotNull($user);

        $valid = $userPasswordHasher->isPasswordValid($user, $plainPassword);
        $this->assertTrue($valid);

        $registration = $registrationRepository->findOneBySelector('max');
        $this->assertSame(UserRegistrationStateEnum::USED->value, $registration->getState());
    }

    public function testCompleteUserRegistrationIfMultipleActiveExistForEmail(): void
    {
        $registerer = $this->getUserRegisterer();
        $userRepository = $this->getUserRepository();
        $registrationRepository = $this->getUserRegistrationRepository();
        $registration1 = $registrationRepository->findOneBySelector('ro1');

        $registerer->completeUserRegistration($registration1, '123456', true);
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

        $registerer->completeUserRegistration($registration1, '123456', true);
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

        $registerer->completeUserRegistration($registration1, '123456', true);
        $this->assertTrue($userRepository->isEmailRegistered('tim@gmail.com'));

        $registration1 = $registrationRepository->findOneBySelector('ti1');
        $registration2 = $registrationRepository->findOneBySelector('ti2');
        $this->assertSame(UserRegistrationStateEnum::USED->value, $registration1->getState());
        $this->assertSame(UserRegistrationStateEnum::DISABLED->value, $registration2->getState());
    }

    public function testCompleteUserRegistrationIfOneIsUsed(): void
    {
        $registerer = $this->getUserRegisterer();
        $userRepository = $this->getUserRepository();
        $registrationRepository = $this->getUserRegistrationRepository();
        $registration1 = $registrationRepository->findOneBySelector('al1');

        $registerer->completeUserRegistration($registration1, '123456', true);
        $this->assertTrue($userRepository->isEmailRegistered('alena@gmail.com'));

        $registration1 = $registrationRepository->findOneBySelector('al1');
        $registration2 = $registrationRepository->findOneBySelector('al2');
        $this->assertSame(UserRegistrationStateEnum::USED->value, $registration1->getState());
        $this->assertSame(UserRegistrationStateEnum::USED->value, $registration2->getState());
    }

    public function testCompleteUserRegistrationIfEmailIsRegistered(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();
        $registration1 = $registrationRepository->findOneBySelector('ka1');

        $registerer->completeUserRegistration($registration1, '123456', true);

        $registration1 = $registrationRepository->findOneBySelector('ka1');
        $registration2 = $registrationRepository->findOneBySelector('ka2');
        $this->assertSame(UserRegistrationStateEnum::DISABLED->value, $registration1->getState());
        $this->assertSame(UserRegistrationStateEnum::DISABLED->value, $registration2->getState());
    }

    public function testCompleteUserRegistrationIfItsInactive(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();
        $registration1 = $registrationRepository->findOneBySelector('lu2');
        $registration2 = $registrationRepository->findOneBySelector('ti2');
        $registration3 = $registrationRepository->findOneBySelector('al2');

        $registerer->completeUserRegistration($registration1, '123456', true);
        $registerer->completeUserRegistration($registration2, '123456', true);
        $registerer->completeUserRegistration($registration3, '123456', true);

        $registration1 = $registrationRepository->findOneBySelector('lu2');
        $registration2 = $registrationRepository->findOneBySelector('ti2');
        $registration3 = $registrationRepository->findOneBySelector('al2');

        $this->assertSame(UserRegistrationStateEnum::UNUSED->value, $registration1->getState());
        $this->assertSame(UserRegistrationStateEnum::DISABLED->value, $registration2->getState());
        $this->assertSame(UserRegistrationStateEnum::USED->value, $registration3->getState());
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

    private function getUserPasswordHasher(): UserPasswordHasherInterface
    {
        $container = static::getContainer();

        /** @var UserPasswordHasherInterface $repository */
        $repository = $container->get(UserPasswordHasherInterface::class);

        return $repository;
    }

    private function getUserRegisterer(): UserRegisterer
    {
        $container = static::getContainer();

        /** @var UserRegisterer $registerer */
        $registerer = $container->get(UserRegisterer::class);

        return $registerer;
    }
}