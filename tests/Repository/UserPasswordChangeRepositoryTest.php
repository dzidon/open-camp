<?php

namespace App\Tests\Repository;

use App\Entity\UserPasswordChange;
use App\Repository\UserPasswordChangeRepository;
use App\Repository\UserRepositoryInterface;
use DateTimeImmutable;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class UserPasswordChangeRepositoryTest extends RepositoryTestCase
{
    private UserPasswordChangeRepository $repository;

    public function testSaveAndRemove(): void
    {
        $now = new DateTimeImmutable('now');
        $passwordChange = new UserPasswordChange($now, 'bob', '123');
        $this->repository->saveUserPasswordChange($passwordChange, true);
        $id = $passwordChange->getId();

        $loadedPasswordChange = $this->repository->find($id);
        $this->assertNotNull($loadedPasswordChange);
        $this->assertSame($passwordChange->getId(), $loadedPasswordChange->getId());

        $this->repository->removeUserPasswordChange($passwordChange, true);
        $loadedPasswordChange = $this->repository->find($id);
        $this->assertNull($loadedPasswordChange);
    }

    public function testCreate(): void
    {
        $expireAt = new DateTimeImmutable('now');
        $selector = 'abc';
        $plainVerifier = 'xyz';

        $passwordChange = $this->repository->createUserPasswordChange($expireAt, $selector, $plainVerifier);
        $this->assertSame($expireAt, $passwordChange->getExpireAt());
        $this->assertSame($selector, $passwordChange->getSelector());

        $hasher = $this->getPasswordHasher();
        $valid = $hasher->verify($passwordChange->getVerifier(), $plainVerifier);
        $this->assertTrue($valid);
    }

    public function testFindOneBySelector(): void
    {
        $passwordChange = $this->repository->findOneBySelector('bob');
        $this->assertNull($passwordChange);

        $passwordChange = $this->repository->findOneBySelector('dav');
        $this->assertNotNull($passwordChange);
        $this->assertSame('dav', $passwordChange->getSelector());

        $registration = $this->repository->findOneBySelector('xxx');
        $this->assertNotNull($registration);

        $registration = $this->repository->findOneBySelector('je2');
        $this->assertNotNull($registration);

        $registration = $this->repository->findOneBySelector('xe2');
        $this->assertNotNull($registration);

        $registration = $this->repository->findOneBySelector('ma2');
        $this->assertNotNull($registration);
    }

    public function testFindOneBySelectorActive(): void
    {
        $passwordChange = $this->repository->findOneBySelector('dav', true);
        $this->assertNotNull($passwordChange);

        $registration = $this->repository->findOneBySelector('xxx', true);
        $this->assertNull($registration);

        $registration = $this->repository->findOneBySelector('je2', true);
        $this->assertNull($registration);

        $registration = $this->repository->findOneBySelector('xe2', true);
        $this->assertNull($registration);

        $registration = $this->repository->findOneBySelector('ma2', true);
        $this->assertNull($registration);
    }

    public function testFindOneBySelectorInactive(): void
    {
        $passwordChange = $this->repository->findOneBySelector('dav', false);
        $this->assertNull($passwordChange);

        $registration = $this->repository->findOneBySelector('xxx', false);
        $this->assertNotNull($registration);

        $registration = $this->repository->findOneBySelector('je2', false);
        $this->assertNotNull($registration);

        $registration = $this->repository->findOneBySelector('xe2', false);
        $this->assertNotNull($registration);

        $registration = $this->repository->findOneBySelector('ma2', false);
        $this->assertNotNull($registration);
    }

    public function testFindByUser(): void
    {
        $userRepository = $this->getUserRepository();

        $user = $userRepository->findOneByEmail('kate@gmail.com');
        $passwordChanges = $this->repository->findByUser($user);
        $this->assertCount(2, $passwordChanges);

        $user = $userRepository->findOneByEmail('jeff@gmail.com');
        $passwordChanges = $this->repository->findByUser($user);
        $this->assertCount(2, $passwordChanges);

        $user = $userRepository->findOneByEmail('xena@gmail.com');
        $passwordChanges = $this->repository->findByUser($user);
        $this->assertCount(2, $passwordChanges);

        $user = $userRepository->findOneByEmail('mark@gmail.com');
        $passwordChanges = $this->repository->findByUser($user);
        $this->assertCount(2, $passwordChanges);
    }

    public function testFindByUserActive(): void
    {
        $userRepository = $this->getUserRepository();

        $user = $userRepository->findOneByEmail('kate@gmail.com');
        $passwordChanges = $this->repository->findByUser($user, true);
        $this->assertCount(2, $passwordChanges);
        $selectors = $this->getSelectorsFromCollection($passwordChanges);
        $this->assertContains('ka1', $selectors);
        $this->assertContains('ka2', $selectors);

        $user = $userRepository->findOneByEmail('jeff@gmail.com');
        $passwordChanges = $this->repository->findByUser($user, true);
        $this->assertCount(1, $passwordChanges);
        $selectors = $this->getSelectorsFromCollection($passwordChanges);
        $this->assertContains('je1', $selectors);

        $user = $userRepository->findOneByEmail('xena@gmail.com');
        $passwordChanges = $this->repository->findByUser($user, true);
        $this->assertCount(1, $passwordChanges);
        $selectors = $this->getSelectorsFromCollection($passwordChanges);
        $this->assertContains('xe1', $selectors);

        $user = $userRepository->findOneByEmail('mark@gmail.com');
        $passwordChanges = $this->repository->findByUser($user, true);
        $this->assertCount(1, $passwordChanges);
        $selectors = $this->getSelectorsFromCollection($passwordChanges);
        $this->assertContains('ma1', $selectors);
    }

    public function testFindByUserInactive(): void
    {
        $userRepository = $this->getUserRepository();

        $user = $userRepository->findOneByEmail('kate@gmail.com');
        $passwordChanges = $this->repository->findByUser($user, false);
        $this->assertCount(0, $passwordChanges);

        $user = $userRepository->findOneByEmail('jeff@gmail.com');
        $passwordChanges = $this->repository->findByUser($user, false);
        $this->assertCount(1, $passwordChanges);
        $selectors = $this->getSelectorsFromCollection($passwordChanges);
        $this->assertContains('je2', $selectors);

        $user = $userRepository->findOneByEmail('xena@gmail.com');
        $passwordChanges = $this->repository->findByUser($user, false);
        $this->assertCount(1, $passwordChanges);
        $selectors = $this->getSelectorsFromCollection($passwordChanges);
        $this->assertContains('xe2', $selectors);

        $user = $userRepository->findOneByEmail('mark@gmail.com');
        $passwordChanges = $this->repository->findByUser($user, false);
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

    private function getPasswordHasher(): PasswordHasherInterface
    {
        $container = static::getContainer();

        /** @var PasswordHasherFactoryInterface $hasherFactory */
        $hasherFactory = $container->get(PasswordHasherFactoryInterface::class);

        return $hasherFactory->getPasswordHasher(UserPasswordChange::class);
    }

    private function getUserRepository(): UserRepositoryInterface
    {
        $container = static::getContainer();

        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $container->get(UserRepositoryInterface::class);

        return $userRepository;
    }

    protected function setUp(): void
    {
        parent::setUp();

        /** @var UserPasswordChangeRepository $repository */
        $repository = $this->entityManager->getRepository(UserPasswordChange::class);
        $this->repository = $repository;
    }
}