<?php

namespace App\Tests\Model\Service\UserRegistration;

use App\Model\Service\UserRegistration\UserRegistrationFactory;
use App\Service\Security\Token\TokenSplitterInterface;
use App\Tests\Service\Security\Token\TokenSplitterMock;
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
        $registrationLifespan = $this->getUserRegistrationLifespan();
        $expireAt = new DateTimeImmutable(sprintf('+%s', $registrationLifespan));

        $result = $registrationFactory->createUserRegistration('bob@gmail.com');
        $this->assertFalse($result->isFake());
        $this->assertSame('bob123', $result->getToken());
        $this->assertSame('123', $result->getPlainVerifier());

        $userRegistration = $result->getUserRegistration();
        $this->assertSame('bob', $userRegistration->getSelector());
        $this->assertSame($expireAt->getTimestamp(), $userRegistration->getExpireAt()->getTimestamp());
    }

    public function testCreateUserRegistrationCollisionBeforeFlushing(): void
    {
        $splitterMock = $this->getTokenSplitterMock();
        $splitterMock
            ->addTestToken('bob123')
            ->addTestToken('bob123')
            ->addTestToken('foo321')
        ;

        $registrationFactory = $this->getUserRegistrationFactory();
        $result = $registrationFactory->createUserRegistration('bob@gmail.com');
        $this->assertFalse($result->isFake());
        $this->assertSame('bob123', $result->getToken());
        $this->assertSame('123', $result->getPlainVerifier());

        $result = $registrationFactory->createUserRegistration('bob@gmail.com');
        $this->assertFalse($result->isFake());
        $this->assertSame('foo321', $result->getToken());
        $this->assertSame('321', $result->getPlainVerifier());
    }

    public function testCreateUserRegistrationIfOneExists(): void
    {
        $registrationFactory = $this->getUserRegistrationFactory();

        $result = $registrationFactory->createUserRegistration('max@gmail.com');

        $this->assertFalse($result->isFake());
    }

    public function testCreateUserRegistrationIfEmailIsRegistered(): void
    {
        $registrationFactory = $this->getUserRegistrationFactory();

        $result = $registrationFactory->createUserRegistration('david@gmail.com');

        $this->assertTrue($result->isFake());
    }

    public function testCreateUserRegistrationIfActiveAmountReached(): void
    {
        $registrationFactory = $this->getUserRegistrationFactory();

        $result = $registrationFactory->createUserRegistration('roman@gmail.com');

        $this->assertTrue($result->isFake());
    }

    public function testCreateUserRegistrationIfOneIsActiveAndOneIsTimeExpired(): void
    {
        $registrationFactory = $this->getUserRegistrationFactory();

        $result = $registrationFactory->createUserRegistration('lucas@gmail.com');

        $this->assertFalse($result->isFake());
    }

    public function testCreateUserRegistrationIfOneIsActiveAndOneIsDisabled(): void
    {
        $registrationFactory = $this->getUserRegistrationFactory();

        $result = $registrationFactory->createUserRegistration('tim@gmail.com');

        $this->assertFalse($result->isFake());
    }

    public function testCreateUserRegistrationIfOneIsActiveAndOneIsUsed(): void
    {
        $registrationFactory = $this->getUserRegistrationFactory();

        $result = $registrationFactory->createUserRegistration('alena@gmail.com');

        $this->assertFalse($result->isFake());
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

        $result = $registrationFactory->createUserRegistration('bob@gmail.com');
        $this->assertFalse($result->isFake());
        $this->assertSame('bob123', $result->getToken());
        $this->assertSame('123', $result->getPlainVerifier());

        $userRegistration = $result->getUserRegistration();
        $this->assertSame('bob', $userRegistration->getSelector());
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

    private function getUserRegistrationFactory(): UserRegistrationFactory
    {
        $container = static::getContainer();

        /** @var UserRegistrationFactory $factory */
        $factory = $container->get(UserRegistrationFactory::class);

        return $factory;
    }
}