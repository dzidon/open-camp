<?php

namespace App\Tests\Model\Repository;

use App\Model\Entity\UserPasswordChange;
use App\Model\Repository\UserPasswordChangeRepository;
use App\Model\Repository\UserRepositoryInterface;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests the UserPasswordChange repository.
 */
class UserPasswordChangeRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();

        $now = new DateTimeImmutable('now');
        $passwordChange = new UserPasswordChange($now, 'bob', '123');
        $passwordChangeRepository->saveUserPasswordChange($passwordChange, true);

        $loadedPasswordChange = $passwordChangeRepository->findOneBySelector('bob');
        $this->assertNotNull($loadedPasswordChange);
        $this->assertSame($passwordChange->getId(), $loadedPasswordChange->getId());

        $passwordChangeRepository->removeUserPasswordChange($passwordChange, true);
        $loadedPasswordChange = $passwordChangeRepository->findOneBySelector('bob');
        $this->assertNull($loadedPasswordChange);
    }

    public function testFindOneBySelector(): void
    {
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();

        $passwordChange = $passwordChangeRepository->findOneBySelector('bob');
        $this->assertNull($passwordChange);

        $passwordChange = $passwordChangeRepository->findOneBySelector('dav');
        $this->assertNotNull($passwordChange);
        $this->assertSame('dav', $passwordChange->getSelector());

        $registration = $passwordChangeRepository->findOneBySelector('xxx');
        $this->assertNotNull($registration);

        $registration = $passwordChangeRepository->findOneBySelector('je2');
        $this->assertNotNull($registration);

        $registration = $passwordChangeRepository->findOneBySelector('xe2');
        $this->assertNotNull($registration);

        $registration = $passwordChangeRepository->findOneBySelector('ma2');
        $this->assertNotNull($registration);
    }

    public function testSelectorExists(): void
    {
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();

        $this->assertFalse($passwordChangeRepository->selectorExists('bob'));
        $this->assertTrue($passwordChangeRepository->selectorExists('dav'));
    }

    public function testFindOneBySelectorActive(): void
    {
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();

        $passwordChange = $passwordChangeRepository->findOneBySelector('dav', true);
        $this->assertNotNull($passwordChange);

        $registration = $passwordChangeRepository->findOneBySelector('xxx', true);
        $this->assertNull($registration);

        $registration = $passwordChangeRepository->findOneBySelector('je2', true);
        $this->assertNull($registration);

        $registration = $passwordChangeRepository->findOneBySelector('xe2', true);
        $this->assertNull($registration);

        $registration = $passwordChangeRepository->findOneBySelector('ma2', true);
        $this->assertNull($registration);
    }

    public function testFindOneBySelectorInactive(): void
    {
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();

        $passwordChange = $passwordChangeRepository->findOneBySelector('dav', false);
        $this->assertNull($passwordChange);

        $registration = $passwordChangeRepository->findOneBySelector('xxx', false);
        $this->assertNotNull($registration);

        $registration = $passwordChangeRepository->findOneBySelector('je2', false);
        $this->assertNotNull($registration);

        $registration = $passwordChangeRepository->findOneBySelector('xe2', false);
        $this->assertNotNull($registration);

        $registration = $passwordChangeRepository->findOneBySelector('ma2', false);
        $this->assertNotNull($registration);
    }

    public function testFindByUser(): void
    {
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $userRepository = $this->getUserRepository();

        $user = $userRepository->findOneByEmail('kate@gmail.com');
        $passwordChanges = $passwordChangeRepository->findByUser($user);
        $this->assertCount(2, $passwordChanges);

        $user = $userRepository->findOneByEmail('jeff@gmail.com');
        $passwordChanges = $passwordChangeRepository->findByUser($user);
        $this->assertCount(2, $passwordChanges);

        $user = $userRepository->findOneByEmail('xena@gmail.com');
        $passwordChanges = $passwordChangeRepository->findByUser($user);
        $this->assertCount(2, $passwordChanges);

        $user = $userRepository->findOneByEmail('mark@gmail.com');
        $passwordChanges = $passwordChangeRepository->findByUser($user);
        $this->assertCount(2, $passwordChanges);
    }

    public function testFindByUserActive(): void
    {
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $userRepository = $this->getUserRepository();

        $user = $userRepository->findOneByEmail('kate@gmail.com');
        $passwordChanges = $passwordChangeRepository->findByUser($user, true);
        $this->assertCount(2, $passwordChanges);
        $selectors = $this->getSelectorsFromCollection($passwordChanges);
        $this->assertContains('ka1', $selectors);
        $this->assertContains('ka2', $selectors);

        $user = $userRepository->findOneByEmail('jeff@gmail.com');
        $passwordChanges = $passwordChangeRepository->findByUser($user, true);
        $this->assertCount(1, $passwordChanges);
        $selectors = $this->getSelectorsFromCollection($passwordChanges);
        $this->assertContains('je1', $selectors);

        $user = $userRepository->findOneByEmail('xena@gmail.com');
        $passwordChanges = $passwordChangeRepository->findByUser($user, true);
        $this->assertCount(1, $passwordChanges);
        $selectors = $this->getSelectorsFromCollection($passwordChanges);
        $this->assertContains('xe1', $selectors);

        $user = $userRepository->findOneByEmail('mark@gmail.com');
        $passwordChanges = $passwordChangeRepository->findByUser($user, true);
        $this->assertCount(1, $passwordChanges);
        $selectors = $this->getSelectorsFromCollection($passwordChanges);
        $this->assertContains('ma1', $selectors);
    }

    public function testFindByUserInactive(): void
    {
        $passwordChangeRepository = $this->getUserPasswordChangeRepository();
        $userRepository = $this->getUserRepository();

        $user = $userRepository->findOneByEmail('kate@gmail.com');
        $passwordChanges = $passwordChangeRepository->findByUser($user, false);
        $this->assertCount(0, $passwordChanges);

        $user = $userRepository->findOneByEmail('jeff@gmail.com');
        $passwordChanges = $passwordChangeRepository->findByUser($user, false);
        $this->assertCount(1, $passwordChanges);
        $selectors = $this->getSelectorsFromCollection($passwordChanges);
        $this->assertContains('je2', $selectors);

        $user = $userRepository->findOneByEmail('xena@gmail.com');
        $passwordChanges = $passwordChangeRepository->findByUser($user, false);
        $this->assertCount(1, $passwordChanges);
        $selectors = $this->getSelectorsFromCollection($passwordChanges);
        $this->assertContains('xe2', $selectors);

        $user = $userRepository->findOneByEmail('mark@gmail.com');
        $passwordChanges = $passwordChangeRepository->findByUser($user, false);
        $this->assertCount(1, $passwordChanges);
        $selectors = $this->getSelectorsFromCollection($passwordChanges);
        $this->assertContains('ma2', $selectors);
    }

    private function getSelectorsFromCollection(array $passwordChanges): array
    {
        $selectors = [];

        /** @var UserPasswordChange $passwordChange */
        foreach ($passwordChanges as $passwordChange)
        {
            $selectors[] = $passwordChange->getSelector();
        }

        return $selectors;
    }

    private function getUserRepository(): UserRepositoryInterface
    {
        $container = static::getContainer();

        /** @var UserRepositoryInterface $repository */
        $repository = $container->get(UserRepositoryInterface::class);

        return $repository;
    }

    private function getUserPasswordChangeRepository(): UserPasswordChangeRepository
    {
        $container = static::getContainer();

        /** @var UserPasswordChangeRepository $repository */
        $repository = $container->get(UserPasswordChangeRepository::class);

        return $repository;
    }
}