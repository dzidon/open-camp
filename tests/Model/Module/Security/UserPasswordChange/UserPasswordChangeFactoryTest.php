<?php

namespace App\Tests\Model\Module\Security\UserPasswordChange;

use App\Model\Module\Security\UserPasswordChange\UserPasswordChangeFactory;
use App\Model\Repository\UserPasswordChangeRepositoryInterface;
use App\Model\Repository\UserRepositoryInterface;
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
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $passwordChangeLifespan = $this->getPasswordChangeLifespan();
        $expireAt = new DateTimeImmutable(sprintf('+%s', $passwordChangeLifespan));

        $result = $passwordChangeFactory->createUserPasswordChange('david@gmail.com', true);
        $this->assertFalse($result->isFake());
        $this->assertSame('d4v123', $result->getToken());
        $this->assertSame('123', $result->getPlainVerifier());

        $passwordChange = $result->getUserPasswordChange();
        $this->assertSame('d4v', $passwordChange->getSelector());
        $this->assertSame($expireAt->getTimestamp(), $passwordChange->getExpireAt()->getTimestamp());

        $passwordChange = $passwordChangeRepository->findOneBySelector('d4v', true);
        $this->assertNotNull($passwordChange);
    }

    public function testCreateUserPasswordChangeIfEmailIsNotRegistered(): void
    {
        $splitterMock = $this->getTokenSplitterMock();
        $splitterMock->addTestToken('bob123'); // bob is the selector, 123 is the verifier

        $passwordChangeFactory = $this->getUserPasswordChangeFactory();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();

        $result = $passwordChangeFactory->createUserPasswordChange('bob@gmail.com', true);
        $passwordChange = $passwordChangeRepository->findOneBySelector('bob', true);

        $this->assertTrue($result->isFake());
        $this->assertNull($passwordChange);
    }

    public function testCreateUserPasswordChangeIfActiveAmountReached(): void
    {
        $passwordChangeFactory = $this->getUserPasswordChangeFactory();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $userRepository = $this->getUserRepository();

        $user = $userRepository->findOneByEmail('kate@gmail.com');
        $result = $passwordChangeFactory->createUserPasswordChange('kate@gmail.com', true);
        $passwordChanges = $passwordChangeRepository->findByUser($user, true);

        $this->assertTrue($result->isFake());
        $this->assertCount(2, $passwordChanges);
    }

    public function testCreateUserPasswordChangeIfOneIsActiveAndOneIsTimeExpired(): void
    {
        $passwordChangeFactory = $this->getUserPasswordChangeFactory();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $userRepository = $this->getUserRepository();

        $user = $userRepository->findOneByEmail('jeff@gmail.com');
        $result = $passwordChangeFactory->createUserPasswordChange('jeff@gmail.com', true);
        $passwordChanges = $passwordChangeRepository->findByUser($user, true);

        $this->assertFalse($result->isFake());
        $this->assertCount(2, $passwordChanges);
    }

    public function testCreateUserPasswordChangeIfOneIsActiveAndOneIsDisabled(): void
    {
        $passwordChangeFactory = $this->getUserPasswordChangeFactory();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $userRepository = $this->getUserRepository();

        $user = $userRepository->findOneByEmail('xena@gmail.com');
        $result = $passwordChangeFactory->createUserPasswordChange('xena@gmail.com', true);
        $passwordChanges = $passwordChangeRepository->findByUser($user, true);

        $this->assertFalse($result->isFake());
        $this->assertCount(2, $passwordChanges);
    }

    public function testCreateUserPasswordChangeIfOneIsActiveAndOneIsUsed(): void
    {
        $passwordChangeFactory = $this->getUserPasswordChangeFactory();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $userRepository = $this->getUserRepository();

        $user = $userRepository->findOneByEmail('mark@gmail.com');
        $result = $passwordChangeFactory->createUserPasswordChange('mark@gmail.com', true);
        $passwordChanges = $passwordChangeRepository->findByUser($user, true);

        $this->assertFalse($result->isFake());
        $this->assertCount(2, $passwordChanges);
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
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();

        $result = $passwordChangeFactory->createUserPasswordChange('david@gmail.com', true);
        $this->assertFalse($result->isFake());

        $passwordChange = $passwordChangeRepository->findOneBySelector('d4v', true);
        $this->assertNotNull($passwordChange);
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

    private function getUserRepository(): UserRepositoryInterface
    {
        $container = static::getContainer();

        /** @var UserRepositoryInterface $repository */
        $repository = $container->get(UserRepositoryInterface::class);

        return $repository;
    }

    private function getUserPasswordChangeRepository(): UserPasswordChangeRepositoryInterface
    {
        $container = static::getContainer();

        /** @var UserPasswordChangeRepositoryInterface $repository */
        $repository = $container->get(UserPasswordChangeRepositoryInterface::class);

        return $repository;
    }

    private function getUserPasswordChangeFactory(): UserPasswordChangeFactory
    {
        $container = static::getContainer();

        /** @var UserPasswordChangeFactory $factory */
        $factory = $container->get(UserPasswordChangeFactory::class);

        return $factory;
    }
}