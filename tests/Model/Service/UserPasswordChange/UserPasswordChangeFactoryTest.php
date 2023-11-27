<?php

namespace App\Tests\Model\Service\UserPasswordChange;

use App\Model\Service\UserPasswordChange\UserPasswordChangeFactory;
use App\Service\Security\Token\TokenSplitterInterface;
use App\Tests\Service\Security\Token\TokenSplitterMock;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Tests creating user password changes.
 */
class UserPasswordChangeFactoryTest extends KernelTestCase
{
    public function testCreateUserPasswordChange(): void
    {
        $splitterMock = $this->getTokenSplitterMock();
        $splitterMock->addTestToken('d4v123'); // d4v is the selector, 123 is the verifier

        $passwordChangeFactory = $this->getUserPasswordChangeFactory();
        $passwordChangeLifespan = $this->getPasswordChangeLifespan();
        $expireAt = new DateTimeImmutable(sprintf('+%s', $passwordChangeLifespan));

        $result = $passwordChangeFactory->createUserPasswordChange('david@gmail.com');
        $this->assertFalse($result->isFake());
        $this->assertSame('d4v123', $result->getToken());
        $this->assertSame('123', $result->getPlainVerifier());

        $passwordChange = $result->getUserPasswordChange();
        $this->assertSame('d4v', $passwordChange->getSelector());
        $this->assertSame($expireAt->getTimestamp(), $passwordChange->getExpireAt()->getTimestamp());
    }

    public function testCreateUserPasswordChangeIfEmailIsNotRegistered(): void
    {
        $splitterMock = $this->getTokenSplitterMock();
        $splitterMock->addTestToken('bob123'); // bob is the selector, 123 is the verifier

        $passwordChangeFactory = $this->getUserPasswordChangeFactory();
        $result = $passwordChangeFactory->createUserPasswordChange('bob@gmail.com');

        $this->assertTrue($result->isFake());
    }

    public function testCreateUserPasswordChangeIfActiveAmountReached(): void
    {
        $passwordChangeFactory = $this->getUserPasswordChangeFactory();

        $result = $passwordChangeFactory->createUserPasswordChange('kate@gmail.com');

        $this->assertTrue($result->isFake());
    }

    public function testCreateUserPasswordChangeIfOneIsActiveAndOneIsTimeExpired(): void
    {
        $passwordChangeFactory = $this->getUserPasswordChangeFactory();

        $result = $passwordChangeFactory->createUserPasswordChange('jeff@gmail.com');

        $this->assertFalse($result->isFake());
    }

    public function testCreateUserPasswordChangeIfOneIsActiveAndOneIsDisabled(): void
    {
        $passwordChangeFactory = $this->getUserPasswordChangeFactory();

        $result = $passwordChangeFactory->createUserPasswordChange('xena@gmail.com');

        $this->assertFalse($result->isFake());
    }

    public function testCreateUserPasswordChangeIfOneIsActiveAndOneIsUsed(): void
    {
        $passwordChangeFactory = $this->getUserPasswordChangeFactory();

        $result = $passwordChangeFactory->createUserPasswordChange('mark@gmail.com');

        $this->assertFalse($result->isFake());
    }

    public function testCreateUserPasswordChangeIfSelectorExists(): void
    {
        $splitterMock = $this->getTokenSplitterMock();
        $splitterMock
            ->addTestToken('ma1xxx') // ma1 is the selector (this one exists in the test db)
            ->addTestToken('ma2xxx') // ma2 is the selector (this one exists in the test db)
            ->addTestToken('d4vxxx') // d4v is the selector (this one does not exist in the test db)
        ;

        $passwordChangeFactory = $this->getUserPasswordChangeFactory();

        $result = $passwordChangeFactory->createUserPasswordChange('david@gmail.com');
        $this->assertFalse($result->isFake());
        $this->assertSame('d4vxxx', $result->getToken());
        $this->assertSame('xxx', $result->getPlainVerifier());

        $passwordChange = $result->getUserPasswordChange();
        $this->assertSame('d4v', $passwordChange->getSelector());
    }

    private function getPasswordChangeLifespan(): string
    {
        $container = static::getContainer();

        /** @var ParameterBagInterface $paramBag */
        $paramBag = $container->get(ParameterBagInterface::class);

        return $paramBag->get('app.password_change_lifespan');
    }

    private function getTokenSplitterMock(): TokenSplitterMock
    {
        $container = static::getContainer();

        /** @var TokenSplitterMock $splitterMock Configured in services_test.yaml */
        $splitterMock = $container->get(TokenSplitterInterface::class);

        return $splitterMock;
    }

    private function getUserPasswordChangeFactory(): UserPasswordChangeFactory
    {
        $container = static::getContainer();

        /** @var UserPasswordChangeFactory $factory */
        $factory = $container->get(UserPasswordChangeFactory::class);

        return $factory;
    }
}