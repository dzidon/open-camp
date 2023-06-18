<?php

namespace App\Tests\Security\Registration;

use App\Repository\UserRegistrationRepositoryInterface;
use App\Security\Registration\UserRegistrationFactory;
use App\Security\Token\TokenSplitterInterface;
use App\Tests\Security\Token\TokenSplitterMock;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Tests creating user registrations.
 */
class UserRegistrationFactoryTest extends KernelTestCase
{
    public function testCreateUserRegistration(): void
    {
        $splitterMock = $this->getTokenSplitterMock();
        $splitterMock->addTestToken('bob123'); // bob is the selector, 123 is the verifier

        $registrationFactory = $this->getUserRegistrationFactory();
        $registrationRepository = $this->getUserRegistrationRepository();
        $registrationLifespan = $this->getUserRegistrationLifespan();
        $expireAt = new DateTimeImmutable(sprintf('+%s', $registrationLifespan));

        $result = $registrationFactory->createUserRegistration('bob@gmail.com', true);
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
        $registrationFactory = $this->getUserRegistrationFactory();
        $registrationRepository = $this->getUserRegistrationRepository();

        $result = $registrationFactory->createUserRegistration('max@gmail.com', true);
        $activeRegistrations = $registrationRepository->findByEmail('max@gmail.com', true);

        $this->assertFalse($result->isFake());
        $this->assertCount(2, $activeRegistrations);
    }

    public function testCreateUserRegistrationIfEmailIsRegistered(): void
    {
        $registrationFactory = $this->getUserRegistrationFactory();
        $registrationRepository = $this->getUserRegistrationRepository();

        $result = $registrationFactory->createUserRegistration('david@gmail.com', true);
        $activeRegistrations = $registrationRepository->findByEmail('david@gmail.com', true);

        $this->assertTrue($result->isFake());
        $this->assertCount(0, $activeRegistrations);
    }

    public function testCreateUserRegistrationIfActiveAmountReached(): void
    {
        $registrationFactory = $this->getUserRegistrationFactory();
        $registrationRepository = $this->getUserRegistrationRepository();

        $result = $registrationFactory->createUserRegistration('roman@gmail.com', true);
        $activeRegistrations = $registrationRepository->findByEmail('roman@gmail.com', true);

        $this->assertTrue($result->isFake());
        $this->assertCount(2, $activeRegistrations);
    }

    public function testCreateUserRegistrationIfOneIsActiveAndOneIsTimeExpired(): void
    {
        $registrationFactory = $this->getUserRegistrationFactory();
        $registrationRepository = $this->getUserRegistrationRepository();

        $result = $registrationFactory->createUserRegistration('lucas@gmail.com', true);
        $activeRegistrations = $registrationRepository->findByEmail('lucas@gmail.com', true);

        $this->assertFalse($result->isFake());
        $this->assertCount(2, $activeRegistrations);
    }

    public function testCreateUserRegistrationIfOneIsActiveAndOneIsDisabled(): void
    {
        $registrationFactory = $this->getUserRegistrationFactory();
        $registrationRepository = $this->getUserRegistrationRepository();

        $result = $registrationFactory->createUserRegistration('tim@gmail.com', true);
        $activeRegistrations = $registrationRepository->findByEmail('tim@gmail.com', true);

        $this->assertFalse($result->isFake());
        $this->assertCount(2, $activeRegistrations);
    }

    public function testCreateUserRegistrationIfOneIsActiveAndOneIsUsed(): void
    {
        $registrationFactory = $this->getUserRegistrationFactory();
        $registrationRepository = $this->getUserRegistrationRepository();

        $result = $registrationFactory->createUserRegistration('alena@gmail.com', true);
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

        $registrationFactory = $this->getUserRegistrationFactory();
        $registrationRepository = $this->getUserRegistrationRepository();

        $result = $registrationFactory->createUserRegistration('bob@gmail.com', true);
        $this->assertFalse($result->isFake());

        $registration = $registrationRepository->findOneBySelector('bob', true);
        $this->assertNotNull($registration);
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

    private function getUserRegistrationRepository(): UserRegistrationRepositoryInterface
    {
        $container = static::getContainer();

        /** @var UserRegistrationRepositoryInterface $repository */
        $repository = $container->get(UserRegistrationRepositoryInterface::class);

        return $repository;
    }

    private function getUserRegistrationFactory(): UserRegistrationFactory
    {
        $container = static::getContainer();

        /** @var UserRegistrationFactory $factory */
        $factory = $container->get(UserRegistrationFactory::class);

        return $factory;
    }
}