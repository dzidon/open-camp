<?php

namespace App\Tests\Model\Service\UserRegistration;

use App\Model\Entity\UserRegistration;
use App\Model\Enum\Entity\UserRegistrationStateEnum;
use App\Model\Repository\UserRegistrationRepositoryInterface;
use App\Model\Service\UserRegistration\UserRegisterer;
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
        $registrationRepository = $this->getUserRegistrationRepository();
        $userPasswordHasher = $this->getUserPasswordHasher();
        $registration = $registrationRepository->findOneBySelector('max');

        $result = $registerer->completeUserRegistration($registration, $plainPassword);

        $user = $result->getUser();
        $valid = $userPasswordHasher->isPasswordValid($user, $plainPassword);
        $this->assertTrue($valid);

        $usedRegistration = $result->getUsedUserRegistration();
        $this->assertSame(UserRegistrationStateEnum::USED, $usedRegistration->getState());
        $this->assertSame($registration, $usedRegistration);

        $disabledRegistrations = $result->getDisabledUserRegistrations();
        $this->assertEmpty($disabledRegistrations);
    }

    public function testCompleteUserRegistrationIfMultipleActiveExistForEmail(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();
        $registration = $registrationRepository->findOneBySelector('ro1');

        $result = $registerer->completeUserRegistration($registration, '123456');
        $user = $result->getUser();
        $usedUserRegistration = $result->getUsedUserRegistration();
        $disabledUserRegistrations = $result->getDisabledUserRegistrations();
        $disabledUserRegistrationSelectors = $this->getUserRegistrationSelectors($disabledUserRegistrations);

        $this->assertSame('roman@gmail.com', $user->getEmail());
        $this->assertSame(UserRegistrationStateEnum::USED, $usedUserRegistration->getState());
        $this->assertSame($registration, $usedUserRegistration);

        $this->assertCount(1, $disabledUserRegistrationSelectors);
        $this->assertContains('ro2', $disabledUserRegistrationSelectors);
    }

    public function testCompleteUserRegistrationIfOneIsTimeExpired(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();
        $registration = $registrationRepository->findOneBySelector('lu1');

        $result = $registerer->completeUserRegistration($registration, '123456');
        $user = $result->getUser();
        $usedUserRegistration = $result->getUsedUserRegistration();
        $disabledUserRegistrations = $result->getDisabledUserRegistrations();

        $this->assertSame('lucas@gmail.com', $user->getEmail());
        $this->assertSame(UserRegistrationStateEnum::USED, $usedUserRegistration->getState());
        $this->assertSame($registration, $usedUserRegistration);
        $this->assertEmpty($disabledUserRegistrations);
    }

    public function testCompleteUserRegistrationIfOneIsDisabled(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();
        $registration = $registrationRepository->findOneBySelector('ti1');

        $result = $registerer->completeUserRegistration($registration, '123456');
        $user = $result->getUser();
        $usedUserRegistration = $result->getUsedUserRegistration();
        $disabledUserRegistrations = $result->getDisabledUserRegistrations();

        $this->assertSame('tim@gmail.com', $user->getEmail());
        $this->assertSame(UserRegistrationStateEnum::USED, $usedUserRegistration->getState());
        $this->assertSame($registration, $usedUserRegistration);
        $this->assertEmpty($disabledUserRegistrations);
    }

    public function testCompleteUserRegistrationIfOneIsUsed(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();
        $registration = $registrationRepository->findOneBySelector('al1');

        $result = $registerer->completeUserRegistration($registration, '123456');
        $user = $result->getUser();
        $usedUserRegistration = $result->getUsedUserRegistration();
        $disabledUserRegistrations = $result->getDisabledUserRegistrations();

        $this->assertSame('alena@gmail.com', $user->getEmail());
        $this->assertSame(UserRegistrationStateEnum::USED, $usedUserRegistration->getState());
        $this->assertSame($registration, $usedUserRegistration);
        $this->assertEmpty($disabledUserRegistrations);
    }

    public function testCompleteUserRegistrationIfEmailIsRegistered(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();
        $registration = $registrationRepository->findOneBySelector('ka1');

        $result = $registerer->completeUserRegistration($registration, '123456');
        $user = $result->getUser();
        $usedUserRegistration = $result->getUsedUserRegistration();
        $disabledUserRegistrations = $result->getDisabledUserRegistrations();
        $disabledUserRegistrationSelectors = $this->getUserRegistrationSelectors($disabledUserRegistrations);

        $this->assertNull($user);
        $this->assertNull($usedUserRegistration);
        $this->assertCount(2, $disabledUserRegistrationSelectors);
        $this->assertContains('ka1', $disabledUserRegistrationSelectors);
        $this->assertContains('ka2', $disabledUserRegistrationSelectors);
    }

    public function testCompleteUserRegistrationIfItsInactive(): void
    {
        $registerer = $this->getUserRegisterer();
        $registrationRepository = $this->getUserRegistrationRepository();
        $registration1 = $registrationRepository->findOneBySelector('lu2');
        $registration2 = $registrationRepository->findOneBySelector('ti2');
        $registration3 = $registrationRepository->findOneBySelector('al2');

        $result1 = $registerer->completeUserRegistration($registration1, '123456');
        $result2 = $registerer->completeUserRegistration($registration2, '123456');
        $result3 = $registerer->completeUserRegistration($registration3, '123456');

        $user1 = $result1->getUser();
        $usedRegistration1 = $result1->getUsedUserRegistration();
        $disabledRegistrations1 = $result1->getDisabledUserRegistrations();
        $this->assertNull($user1);
        $this->assertNull($usedRegistration1);
        $this->assertEmpty($disabledRegistrations1);
        $this->assertSame(UserRegistrationStateEnum::UNUSED, $registration1->getState());

        $user2 = $result2->getUser();
        $usedRegistration2 = $result2->getUsedUserRegistration();
        $disabledRegistrations2 = $result2->getDisabledUserRegistrations();
        $this->assertNull($user2);
        $this->assertNull($usedRegistration2);
        $this->assertEmpty($disabledRegistrations2);
        $this->assertSame(UserRegistrationStateEnum::DISABLED, $registration2->getState());

        $user3 = $result3->getUser();
        $usedRegistration3 = $result3->getUsedUserRegistration();
        $disabledRegistrations3 = $result3->getDisabledUserRegistrations();
        $this->assertNull($user3);
        $this->assertNull($usedRegistration3);
        $this->assertEmpty($disabledRegistrations3);
        $this->assertSame(UserRegistrationStateEnum::USED, $registration3->getState());
    }

    private function getUserRegistrationSelectors(array $userRegistrations): array
    {
        $selectors = [];

        /** @var UserRegistration $userRegistration */
        foreach ($userRegistrations as $userRegistration)
        {
            $selectors[] = $userRegistration->getSelector();
        }

        return $selectors;
    }

    private function getUserRegistrationRepository(): UserRegistrationRepositoryInterface
    {
        $container = static::getContainer();

        /** @var UserRegistrationRepositoryInterface $repository */
        $repository = $container->get(UserRegistrationRepositoryInterface::class);

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