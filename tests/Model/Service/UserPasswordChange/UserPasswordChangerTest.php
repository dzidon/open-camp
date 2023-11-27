<?php

namespace App\Tests\Model\Service\UserPasswordChange;

use App\Model\Entity\UserPasswordChange;
use App\Model\Enum\Entity\UserPasswordChangeStateEnum;
use App\Model\Repository\UserPasswordChangeRepositoryInterface;
use App\Model\Service\UserPasswordChange\UserPasswordChanger;
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
        $userPasswordHasher = $this->getUserPasswordHasher();
        $passwordChange = $passwordChangeRepository->findOneBySelector('dav');
        $user = $passwordChange->getUser();

        $result = $passwordChanger->completeUserPasswordChange($passwordChange, $plainPassword);
        $valid = $userPasswordHasher->isPasswordValid($user, $plainPassword);
        $this->assertTrue($valid);

        $usedPasswordChange = $result->getUsedUserPasswordChange();
        $this->assertSame($usedPasswordChange, $passwordChange);
        $this->assertSame(UserPasswordChangeStateEnum::USED, $usedPasswordChange->getState());
        $this->assertEmpty($result->getDisabledUserPasswordChanges());
    }

    public function testCompletePasswordChangeIfMultipleActiveExistForUser(): void
    {
        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $passwordChange = $passwordChangeRepository->findOneBySelector('ka1');

        $result = $passwordChanger->completeUserPasswordChange($passwordChange, '123456');
        $usedUserPasswordChange = $result->getUsedUserPasswordChange();
        $disabledUserPasswordChanges = $result->getDisabledUserPasswordChanges();
        $disabledUserPasswordChangeSelectors = $this->getUserPasswordChangeSelectors($disabledUserPasswordChanges);

        $this->assertSame(UserPasswordChangeStateEnum::USED, $usedUserPasswordChange->getState());
        $this->assertSame($passwordChange, $usedUserPasswordChange);

        $this->assertCount(1, $disabledUserPasswordChangeSelectors);
        $this->assertContains('ka2', $disabledUserPasswordChangeSelectors);
    }

    public function testCompletePasswordChangeIfOneIsTimeExpired(): void
    {
        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $passwordChange = $passwordChangeRepository->findOneBySelector('je1');

        $result = $passwordChanger->completeUserPasswordChange($passwordChange, '123456');
        $usedUserPasswordChange = $result->getUsedUserPasswordChange();
        $disabledUserPasswordChanges = $result->getDisabledUserPasswordChanges();

        $this->assertSame(UserPasswordChangeStateEnum::USED, $usedUserPasswordChange->getState());
        $this->assertSame($passwordChange, $usedUserPasswordChange);
        $this->assertEmpty($disabledUserPasswordChanges);
    }

    public function testCompletePasswordChangeIfOneIsDisabled(): void
    {
        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $passwordChange = $passwordChangeRepository->findOneBySelector('xe1');

        $result = $passwordChanger->completeUserPasswordChange($passwordChange, '123456');
        $usedUserPasswordChange = $result->getUsedUserPasswordChange();
        $disabledUserPasswordChanges = $result->getDisabledUserPasswordChanges();

        $this->assertSame(UserPasswordChangeStateEnum::USED, $usedUserPasswordChange->getState());
        $this->assertSame($passwordChange, $usedUserPasswordChange);
        $this->assertEmpty($disabledUserPasswordChanges);
    }

    public function testCompletePasswordChangeIfOneIsUsed(): void
    {
        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $passwordChange = $passwordChangeRepository->findOneBySelector('ma1');

        $result = $passwordChanger->completeUserPasswordChange($passwordChange, '123456');
        $usedUserPasswordChange = $result->getUsedUserPasswordChange();
        $disabledUserPasswordChanges = $result->getDisabledUserPasswordChanges();

        $this->assertSame(UserPasswordChangeStateEnum::USED, $usedUserPasswordChange->getState());
        $this->assertSame($passwordChange, $usedUserPasswordChange);
        $this->assertEmpty($disabledUserPasswordChanges);
    }

    public function testCompletePasswordChangeIfItsInactive(): void
    {
        $passwordChanger = $this->getUserPasswordChanger();
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $passwordChange1 = $passwordChangeRepository->findOneBySelector('xxx');
        $passwordChange2 = $passwordChangeRepository->findOneBySelector('je2');
        $passwordChange3 = $passwordChangeRepository->findOneBySelector('xe2');
        $passwordChange4 = $passwordChangeRepository->findOneBySelector('ma2');

        $result1 = $passwordChanger->completeUserPasswordChange($passwordChange1, '123456');
        $result2 = $passwordChanger->completeUserPasswordChange($passwordChange2, '123456');
        $result3 = $passwordChanger->completeUserPasswordChange($passwordChange3, '123456');
        $result4 = $passwordChanger->completeUserPasswordChange($passwordChange4, '123456');

        $usedUserPasswordChange1 = $result1->getUsedUserPasswordChange();
        $disabledUserPasswordChanges1 = $result1->getDisabledUserPasswordChanges();
        $this->assertNull($usedUserPasswordChange1);
        $this->assertEmpty($disabledUserPasswordChanges1);
        $this->assertSame(UserPasswordChangeStateEnum::UNUSED, $passwordChange1->getState());

        $usedUserPasswordChange2 = $result2->getUsedUserPasswordChange();
        $disabledUserPasswordChanges2 = $result2->getDisabledUserPasswordChanges();
        $this->assertNull($usedUserPasswordChange2);
        $this->assertEmpty($disabledUserPasswordChanges2);
        $this->assertSame(UserPasswordChangeStateEnum::UNUSED, $passwordChange2->getState());

        $usedUserPasswordChange3 = $result3->getUsedUserPasswordChange();
        $disabledUserPasswordChanges3 = $result3->getDisabledUserPasswordChanges();
        $this->assertNull($usedUserPasswordChange3);
        $this->assertEmpty($disabledUserPasswordChanges3);
        $this->assertSame(UserPasswordChangeStateEnum::DISABLED, $passwordChange3->getState());

        $usedUserPasswordChange4 = $result4->getUsedUserPasswordChange();
        $disabledUserPasswordChanges4 = $result4->getDisabledUserPasswordChanges();
        $this->assertNull($usedUserPasswordChange4);
        $this->assertEmpty($disabledUserPasswordChanges4);
        $this->assertSame(UserPasswordChangeStateEnum::USED, $passwordChange4->getState());
    }

    private function getUserPasswordChangeSelectors(array $userPasswordChanges): array
    {
        $selectors = [];

        /** @var UserPasswordChange $userPasswordChange */
        foreach ($userPasswordChanges as $userPasswordChange)
        {
            $selectors[] = $userPasswordChange->getSelector();
        }

        return $selectors;
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

    private function getUserPasswordChanger(): UserPasswordChanger
    {
        $container = static::getContainer();

        /** @var UserPasswordChanger $userPasswordChanger */
        $userPasswordChanger = $container->get(UserPasswordChanger::class);

        return $userPasswordChanger;
    }
}