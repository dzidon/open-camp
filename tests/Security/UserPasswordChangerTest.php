<?php

namespace App\Tests\Security;

use App\Enum\Entity\UserPasswordChangeStateEnum;
use App\Repository\UserPasswordChangeRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Security\TokenSplitterInterface;
use App\Security\UserPasswordChanger;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordChangerTest extends KernelTestCase
{
    public function testCreateUserPasswordChange(): void
    {
        $splitterMock = $this->getTokenSplitterMock();
        $splitterMock->addTestToken('d4v123'); // d4v is the selector, 123 is the verifier

        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $passwordChangeLifespan = $this->getPasswordChangeLifespan();
        $expireAt = new DateTimeImmutable(sprintf('+%s', $passwordChangeLifespan));

        $result = $passwordChanger->createUserPasswordChange('david@gmail.com', true);
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

        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();

        $result = $passwordChanger->createUserPasswordChange('bob@gmail.com', true);
        $passwordChange = $passwordChangeRepository->findOneBySelector('bob', true);

        $this->assertTrue($result->isFake());
        $this->assertNull($passwordChange);
    }

    public function testCreateUserPasswordChangeIfActiveAmountReached(): void
    {
        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $userRepository = $this->getUserRepository();

        $user = $userRepository->findOneByEmail('kate@gmail.com');
        $result = $passwordChanger->createUserPasswordChange('kate@gmail.com', true);
        $passwordChanges = $passwordChangeRepository->findByUser($user, true);

        $this->assertTrue($result->isFake());
        $this->assertCount(2, $passwordChanges);
    }

    public function testCreateUserPasswordChangeIfOneIsActiveAndOneIsTimeExpired(): void
    {
        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $userRepository = $this->getUserRepository();

        $user = $userRepository->findOneByEmail('jeff@gmail.com');
        $result = $passwordChanger->createUserPasswordChange('jeff@gmail.com', true);
        $passwordChanges = $passwordChangeRepository->findByUser($user, true);

        $this->assertFalse($result->isFake());
        $this->assertCount(2, $passwordChanges);
    }

    public function testCreateUserPasswordChangeIfOneIsActiveAndOneIsDisabled(): void
    {
        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $userRepository = $this->getUserRepository();

        $user = $userRepository->findOneByEmail('xena@gmail.com');
        $result = $passwordChanger->createUserPasswordChange('xena@gmail.com', true);
        $passwordChanges = $passwordChangeRepository->findByUser($user, true);

        $this->assertFalse($result->isFake());
        $this->assertCount(2, $passwordChanges);
    }

    public function testCreateUserPasswordChangeIfOneIsActiveAndOneIsUsed(): void
    {
        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $userRepository = $this->getUserRepository();

        $user = $userRepository->findOneByEmail('mark@gmail.com');
        $result = $passwordChanger->createUserPasswordChange('mark@gmail.com', true);
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

        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();

        $result = $passwordChanger->createUserPasswordChange('david@gmail.com', true);
        $this->assertFalse($result->isFake());

        $passwordChange = $passwordChangeRepository->findOneBySelector('d4v', true);
        $this->assertNotNull($passwordChange);
    }

    public function testVerify(): void
    {
        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $passwordChange = $passwordChangeRepository->findOneBySelector('dav');

        $this->assertTrue($passwordChanger->verify($passwordChange, '123'));
        $this->assertFalse($passwordChanger->verify($passwordChange, '321'));
    }

    public function testCompletePasswordChange(): void
    {
        $plainPassword = 'new_password';
        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $userRepository = $this->getUserRepository();
        $userPasswordHasher = $this->getUserPasswordHasher();
        $passwordChange = $passwordChangeRepository->findOneBySelector('dav');

        $passwordChanger->completeUserPasswordChange($passwordChange, $plainPassword, true);

        $user = $userRepository->findOneByEmail('david@gmail.com');

        $valid = $userPasswordHasher->isPasswordValid($user, $plainPassword);
        $this->assertTrue($valid);

        $passwordChange = $passwordChangeRepository->findOneBySelector('dav');
        $this->assertSame(UserPasswordChangeStateEnum::USED->value, $passwordChange->getState());
    }

    public function testCompletePasswordChangeIfMultipleActiveExistForUser(): void
    {
        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $passwordChange1 = $passwordChangeRepository->findOneBySelector('ka1');

        $passwordChanger->completeUserPasswordChange($passwordChange1, '123456', true);

        $passwordChange1 = $passwordChangeRepository->findOneBySelector('ka1');
        $passwordChange2 = $passwordChangeRepository->findOneBySelector('ka2');
        $this->assertSame(UserPasswordChangeStateEnum::USED->value, $passwordChange1->getState());
        $this->assertSame(UserPasswordChangeStateEnum::DISABLED->value, $passwordChange2->getState());
    }

    public function testCompletePasswordChangeIfOneIsTimeExpired(): void
    {
        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $passwordChange1 = $passwordChangeRepository->findOneBySelector('je1');

        $passwordChanger->completeUserPasswordChange($passwordChange1, '123456', true);

        $passwordChange1 = $passwordChangeRepository->findOneBySelector('je1');
        $passwordChange2 = $passwordChangeRepository->findOneBySelector('je2');
        $this->assertSame(UserPasswordChangeStateEnum::USED->value, $passwordChange1->getState());
        $this->assertSame(UserPasswordChangeStateEnum::UNUSED->value, $passwordChange2->getState());
    }

    public function testCompletePasswordChangeIfOneIsDisabled(): void
    {
        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $passwordChange1 = $passwordChangeRepository->findOneBySelector('xe1');

        $passwordChanger->completeUserPasswordChange($passwordChange1, '123456', true);

        $passwordChange1 = $passwordChangeRepository->findOneBySelector('xe1');
        $passwordChange2 = $passwordChangeRepository->findOneBySelector('xe2');
        $this->assertSame(UserPasswordChangeStateEnum::USED->value, $passwordChange1->getState());
        $this->assertSame(UserPasswordChangeStateEnum::DISABLED->value, $passwordChange2->getState());
    }

    public function testCompletePasswordChangeIfOneIsUsed(): void
    {
        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $passwordChange1 = $passwordChangeRepository->findOneBySelector('ma1');

        $passwordChanger->completeUserPasswordChange($passwordChange1, '123456', true);

        $passwordChange1 = $passwordChangeRepository->findOneBySelector('ma1');
        $passwordChange2 = $passwordChangeRepository->findOneBySelector('ma2');
        $this->assertSame(UserPasswordChangeStateEnum::USED->value, $passwordChange1->getState());
        $this->assertSame(UserPasswordChangeStateEnum::USED->value, $passwordChange2->getState());
    }

    public function testCompletePasswordChangeIfItsInactive(): void
    {
        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $passwordChange1 = $passwordChangeRepository->findOneBySelector('xxx');
        $passwordChange2 = $passwordChangeRepository->findOneBySelector('je2');
        $passwordChange3 = $passwordChangeRepository->findOneBySelector('xe2');
        $passwordChange4 = $passwordChangeRepository->findOneBySelector('ma2');

        $passwordChanger->completeUserPasswordChange($passwordChange1, '123456', true);
        $passwordChanger->completeUserPasswordChange($passwordChange2, '123456', true);
        $passwordChanger->completeUserPasswordChange($passwordChange3, '123456', true);
        $passwordChanger->completeUserPasswordChange($passwordChange4, '123456', true);

        $passwordChange1 = $passwordChangeRepository->findOneBySelector('xxx');
        $passwordChange2 = $passwordChangeRepository->findOneBySelector('je2');
        $passwordChange3 = $passwordChangeRepository->findOneBySelector('xe2');
        $passwordChange4 = $passwordChangeRepository->findOneBySelector('ma2');

        $this->assertSame(UserPasswordChangeStateEnum::UNUSED->value, $passwordChange1->getState());
        $this->assertSame(UserPasswordChangeStateEnum::UNUSED->value, $passwordChange2->getState());
        $this->assertSame(UserPasswordChangeStateEnum::DISABLED->value, $passwordChange3->getState());
        $this->assertSame(UserPasswordChangeStateEnum::USED->value, $passwordChange4->getState());
    }

    private function getPasswordChangeLifespan(): string
    {
        $container = static::getContainer();

        /** @var ParameterBagInterface $paramBag */
        $paramBag = $container->get(ParameterBagInterface::class);

        return $paramBag->get('app.password_change_lifespan');
    }

    private function getUserPasswordHasher(): UserPasswordHasherInterface
    {
        $container = static::getContainer();

        /** @var UserPasswordHasherInterface $repository */
        $repository = $container->get(UserPasswordHasherInterface::class);

        return $repository;
    }

    private function getTokenSplitterMock(): TokenSplitterMock
    {
        $container = static::getContainer();

        /** @var TokenSplitterMock $splitterMock Configured in services_test.yaml */
        $splitterMock = $container->get(TokenSplitterInterface::class);

        return $splitterMock;
    }

    private function getUserPasswordChangeRepository(): UserPasswordChangeRepositoryInterface
    {
        $container = static::getContainer();

        /** @var UserPasswordChangeRepositoryInterface $repository */
        $repository = $container->get(UserPasswordChangeRepositoryInterface::class);

        return $repository;
    }

    private function getUserRepository(): UserRepositoryInterface
    {
        $container = static::getContainer();

        /** @var UserRepositoryInterface $repository */
        $repository = $container->get(UserRepositoryInterface::class);

        return $repository;
    }

    private function getUserPasswordChanger(): UserPasswordChanger
    {
        $container = static::getContainer();

        /** @var UserPasswordChanger $userPasswordChanger */
        $userPasswordChanger = $container->get(UserPasswordChanger::class);

        return $userPasswordChanger;
    }
}