<?php

namespace App\Tests\Repository;

use App\Entity\UserRegistration;
use App\Repository\UserRegistrationRepository;
use DateTimeImmutable;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class UserRegistrationRepositoryTest extends RepositoryTestCase
{
    private UserRegistrationRepository $repository;

    public function testSaveAndRemove(): void
    {
        $now = new DateTimeImmutable('now');
        $registration = new UserRegistration('bob@bing.com', $now, 'bob', '123');
        $this->repository->saveUserRegistration($registration, true);
        $id = $registration->getId();

        $loadedRegistration = $this->repository->find($id);
        $this->assertNotNull($loadedRegistration);
        $this->assertSame($registration->getId(), $loadedRegistration->getId());

        $this->repository->removeUserRegistration($registration, true);
        $loadedRegistration = $this->repository->find($id);
        $this->assertNull($loadedRegistration);
    }

    public function testCreate(): void
    {
        $email = 'bob@gmail.com';
        $expireAt = new DateTimeImmutable('now');
        $selector = 'abc';
        $plainVerifier = 'xyz';

        $userRegistration = $this->repository->createUserRegistration($email, $expireAt, $selector, $plainVerifier);
        $this->assertSame($email, $userRegistration->getEmail());
        $this->assertSame($expireAt, $userRegistration->getExpireAt());
        $this->assertSame($selector, $userRegistration->getSelector());

        $hasher = $this->getPasswordHasher();
        $valid = $hasher->verify($userRegistration->getVerifier(), $plainVerifier);
        $this->assertTrue($valid);
    }

    public function testFindOneBySelector(): void
    {
        $registration = $this->repository->findOneBySelector('bob');
        $this->assertNull($registration);

        $registration = $this->repository->findOneBySelector('max');
        $this->assertNotNull($registration);
        $this->assertSame('max', $registration->getSelector());

        $registration = $this->repository->findOneBySelector('lu2');
        $this->assertNotNull($registration);

        $registration = $this->repository->findOneBySelector('ti2');
        $this->assertNotNull($registration);

        $registration = $this->repository->findOneBySelector('al2');
        $this->assertNotNull($registration);
    }

    public function testFindOneBySelectorActive(): void
    {
        $registration = $this->repository->findOneBySelector('max', true);
        $this->assertNotNull($registration);

        $registration = $this->repository->findOneBySelector('lu2', true);
        $this->assertNull($registration);

        $registration = $this->repository->findOneBySelector('ti2', true);
        $this->assertNull($registration);

        $registration = $this->repository->findOneBySelector('al2', true);
        $this->assertNull($registration);
    }

    public function testFindOneBySelectorInactive(): void
    {
        $registration = $this->repository->findOneBySelector('max', false);
        $this->assertNull($registration);

        $registration = $this->repository->findOneBySelector('lu2', false);
        $this->assertNotNull($registration);

        $registration = $this->repository->findOneBySelector('ti2', false);
        $this->assertNotNull($registration);

        $registration = $this->repository->findOneBySelector('al2', false);
        $this->assertNotNull($registration);
    }

    public function testFindByEmail(): void
    {
        // bob
        $registrations = $this->repository->findByEmail('bob@gmail.com');
        $this->assertCount(0, $registrations);

        // lucas
        $registrations = $this->repository->findByEmail('lucas@gmail.com');
        $this->assertCount(2, $registrations);

        $selectors = $this->getSelectorsFromCollection($registrations);
        $this->assertContains('lu1', $selectors);
        $this->assertContains('lu2', $selectors);

        // tim
        $registrations = $this->repository->findByEmail('tim@gmail.com');
        $this->assertCount(2, $registrations);

        $selectors = $this->getSelectorsFromCollection($registrations);
        $this->assertContains('ti1', $selectors);
        $this->assertContains('ti2', $selectors);

        // alena
        $registrations = $this->repository->findByEmail('alena@gmail.com');
        $this->assertCount(2, $registrations);

        $selectors = $this->getSelectorsFromCollection($registrations);
        $this->assertContains('al1', $selectors);
        $this->assertContains('al2', $selectors);
    }

    public function testFindByEmailActive(): void
    {
        // lucas
        $registrations = $this->repository->findByEmail('lucas@gmail.com', true);
        $this->assertCount(1, $registrations);

        $selectors = $this->getSelectorsFromCollection($registrations);
        $this->assertContains('lu1', $selectors);

        // tim
        $registrations = $this->repository->findByEmail('tim@gmail.com', true);
        $this->assertCount(1, $registrations);

        $selectors = $this->getSelectorsFromCollection($registrations);
        $this->assertContains('ti1', $selectors);

        // alena
        $registrations = $this->repository->findByEmail('alena@gmail.com', true);
        $this->assertCount(1, $registrations);

        $selectors = $this->getSelectorsFromCollection($registrations);
        $this->assertContains('al1', $selectors);
    }

    public function testFindByEmailInactive(): void
    {
        // lucas
        $registrations = $this->repository->findByEmail('lucas@gmail.com', false);
        $this->assertCount(1, $registrations);

        $selectors = $this->getSelectorsFromCollection($registrations);
        $this->assertContains('lu2', $selectors);

        // tim
        $registrations = $this->repository->findByEmail('tim@gmail.com', false);
        $this->assertCount(1, $registrations);

        $selectors = $this->getSelectorsFromCollection($registrations);
        $this->assertContains('ti2', $selectors);

        // alena
        $registrations = $this->repository->findByEmail('alena@gmail.com', false);
        $this->assertCount(1, $registrations);

        $selectors = $this->getSelectorsFromCollection($registrations);
        $this->assertContains('al2', $selectors);
    }

    private function getSelectorsFromCollection(array $registrations): array
    {
        $selectors = [];

        /** @var UserRegistration $registration */
        foreach ($registrations as $registration)
        {
            $selectors[] = $registration->getSelector();
        }

        return $selectors;
    }

    private function getPasswordHasher(): PasswordHasherInterface
    {
        $container = static::getContainer();

        /** @var PasswordHasherFactoryInterface $hasherFactory */
        $hasherFactory = $container->get(PasswordHasherFactoryInterface::class);

        return $hasherFactory->getPasswordHasher(UserRegistration::class);
    }

    protected function setUp(): void
    {
        parent::setUp();

        /** @var UserRegistrationRepository $repository */
        $repository = $this->entityManager->getRepository(UserRegistration::class);
        $this->repository = $repository;
    }
}