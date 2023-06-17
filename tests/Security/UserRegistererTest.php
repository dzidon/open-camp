<?php

namespace App\Tests\Security;

use App\Enum\Entity\UserRegistrationStateEnum;
use App\Repository\UserRegistrationRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Security\TokenSplitterInterface;
use App\Security\UserRegisterer;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Tests creating, verifying and completing user registrations.
 */
class UserRegistererTest extends KernelTestCase
{
    public function testCreateUserRegistration(): void
    {
        $splitterMock = $this->getTokenSplitterMock();
        $splitterMock->addTestToken('bob123'); // bob is the selector, 123 is the verifier

        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();
        $registrationLifespan = $this->getUserRegistrationLifespan();
        $expireAt = new DateTimeImmutable(sprintf('+%s', $registrationLifespan));

        $result = $registerer->createUserRegistration('bob@gmail.com', true);
        $this->assertFalse($result->isFake());
        $this->assertSame('bob123', $result->getToken());
        $this->assertSame('123', $result->getPlainVerifier());

        $userRegistration = $result->getUserRegistration();
        $this->assertSame('bob', $userRegistration->getSelector());
        $this->assertSame($expireAt->getTimestamp(), $userRegistration->getExpireAt()->getTimestamp());

        $registration = $registrationRepository->findOneBySelector('bob', true);
        $this->assertNotNull($registration);
    }

    public function testCreateUserRegistrationIfOneExists(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();

        $result = $registerer->createUserRegistration('max@gmail.com', true);
        $activeRegistrations = $registrationRepository->findByEmail('max@gmail.com', true);

        $this->assertFalse($result->isFake());
        $this->assertCount(2, $activeRegistrations);
    }

    public function testCreateUserRegistrationIfEmailIsRegistered(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();

        $result = $registerer->createUserRegistration('david@gmail.com', true);
        $activeRegistrations = $registrationRepository->findByEmail('david@gmail.com', true);

        $this->assertTrue($result->isFake());
        $this->assertCount(0, $activeRegistrations);
    }

    public function testCreateUserRegistrationIfActiveAmountReached(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();

        $result = $registerer->createUserRegistration('roman@gmail.com', true);
        $activeRegistrations = $registrationRepository->findByEmail('roman@gmail.com', true);

        $this->assertTrue($result->isFake());
        $this->assertCount(2, $activeRegistrations);
    }

    public function testCreateUserRegistrationIfOneIsActiveAndOneIsTimeExpired(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();

        $result = $registerer->createUserRegistration('lucas@gmail.com', true);
        $activeRegistrations = $registrationRepository->findByEmail('lucas@gmail.com', true);

        $this->assertFalse($result->isFake());
        $this->assertCount(2, $activeRegistrations);
    }

    public function testCreateUserRegistrationIfOneIsActiveAndOneIsDisabled(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();

        $result = $registerer->createUserRegistration('tim@gmail.com', true);
        $activeRegistrations = $registrationRepository->findByEmail('tim@gmail.com', true);

        $this->assertFalse($result->isFake());
        $this->assertCount(2, $activeRegistrations);
    }

    public function testCreateUserRegistrationIfOneIsActiveAndOneIsUsed(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();

        $result = $registerer->createUserRegistration('alena@gmail.com', true);
        $activeRegistrations = $registrationRepository->findByEmail('alena@gmail.com', true);

        $this->assertFalse($result->isFake());
        $this->assertCount(2, $activeRegistrations);
    }

    public function testCreateUserRegistrationIfSelectorExists(): void
    {
        $splitterMock = $this->getTokenSplitterMock();
        $splitterMock
            ->addTestToken('max123') // max is the selector (this one exists in the test db)
            ->addTestToken('ti1xxx') // ti1 is the selector (this one exists in the test db)
            ->addTestToken('bob123') // bob is the selector (this one does not exist in the test db)
        ;

        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();

        $result = $registerer->createUserRegistration('bob@gmail.com', true);
        $this->assertFalse($result->isFake());

        $registration = $registrationRepository->findOneBySelector('bob', true);
        $this->assertNotNull($registration);
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

    private function getUserRegistrationLifespan(): string
    {
        $container = static::getContainer();

        /** @var ParameterBagInterface $paramBag */
        $paramBag = $container->get(ParameterBagInterface::class);

        return $paramBag->get('app.registration_lifespan');
    }

    private function getTokenSplitterMock(): TokenSplitterMock
    {
        $container = static::getContainer();

        /** @var TokenSplitterMock $splitterMock Configured in services_test.yaml */
        $splitterMock = $container->get(TokenSplitterInterface::class);

        return $splitterMock;
    }

    private function getUserPasswordHasher(): UserPasswordHasherInterface
    {
        $container = static::getContainer();

        /** @var UserPasswordHasherInterface $repository */
        $repository = $container->get(UserPasswordHasherInterface::class);

        return $repository;
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
}