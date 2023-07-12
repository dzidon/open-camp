<?php

namespace App\Tests\Model\Module\Security\UserPasswordChange;

use App\Enum\Entity\UserPasswordChangeStateEnum;
use App\Model\Module\Security\UserPasswordChange\UserPasswordChanger;
use App\Model\Repository\UserPasswordChangeRepositoryInterface;
use App\Model\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Tests user password change completion.
 */
class UserPasswordChangerTest extends KernelTestCase
{
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
        $this->assertSame(UserPasswordChangeStateEnum::USED, $passwordChange->getState());
    }

    public function testCompletePasswordChangeIfMultipleActiveExistForUser(): void
    {
        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $passwordChange1 = $passwordChangeRepository->findOneBySelector('ka1');

        $passwordChanger->completeUserPasswordChange($passwordChange1, '123456', true);

        $passwordChange1 = $passwordChangeRepository->findOneBySelector('ka1');
        $passwordChange2 = $passwordChangeRepository->findOneBySelector('ka2');
        $this->assertSame(UserPasswordChangeStateEnum::USED, $passwordChange1->getState());
        $this->assertSame(UserPasswordChangeStateEnum::DISABLED, $passwordChange2->getState());
    }

    public function testCompletePasswordChangeIfOneIsTimeExpired(): void
    {
        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $passwordChange1 = $passwordChangeRepository->findOneBySelector('je1');

        $passwordChanger->completeUserPasswordChange($passwordChange1, '123456', true);

        $passwordChange1 = $passwordChangeRepository->findOneBySelector('je1');
        $passwordChange2 = $passwordChangeRepository->findOneBySelector('je2');
        $this->assertSame(UserPasswordChangeStateEnum::USED, $passwordChange1->getState());
        $this->assertSame(UserPasswordChangeStateEnum::UNUSED, $passwordChange2->getState());
    }

    public function testCompletePasswordChangeIfOneIsDisabled(): void
    {
        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $passwordChange1 = $passwordChangeRepository->findOneBySelector('xe1');

        $passwordChanger->completeUserPasswordChange($passwordChange1, '123456', true);

        $passwordChange1 = $passwordChangeRepository->findOneBySelector('xe1');
        $passwordChange2 = $passwordChangeRepository->findOneBySelector('xe2');
        $this->assertSame(UserPasswordChangeStateEnum::USED, $passwordChange1->getState());
        $this->assertSame(UserPasswordChangeStateEnum::DISABLED, $passwordChange2->getState());
    }

    public function testCompletePasswordChangeIfOneIsUsed(): void
    {
        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $passwordChange1 = $passwordChangeRepository->findOneBySelector('ma1');

        $passwordChanger->completeUserPasswordChange($passwordChange1, '123456', true);

        $passwordChange1 = $passwordChangeRepository->findOneBySelector('ma1');
        $passwordChange2 = $passwordChangeRepository->findOneBySelector('ma2');
        $this->assertSame(UserPasswordChangeStateEnum::USED, $passwordChange1->getState());
        $this->assertSame(UserPasswordChangeStateEnum::USED, $passwordChange2->getState());
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

        $this->assertSame(UserPasswordChangeStateEnum::UNUSED, $passwordChange1->getState());
        $this->assertSame(UserPasswordChangeStateEnum::UNUSED, $passwordChange2->getState());
        $this->assertSame(UserPasswordChangeStateEnum::DISABLED, $passwordChange3->getState());
        $this->assertSame(UserPasswordChangeStateEnum::USED, $passwordChange4->getState());
    }

    private function getUserPasswordHasher(): UserPasswordHasherInterface
    {
        $container = static::getContainer();

        /** @var UserPasswordHasherInterface $repository */
        $repository = $container->get(UserPasswordHasherInterface::class);

        return $repository;
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