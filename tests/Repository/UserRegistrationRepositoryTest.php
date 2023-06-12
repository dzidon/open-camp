<?php

namespace App\Tests\Repository;

use App\Entity\UserRegistration;
use App\Repository\UserRegistrationRepository;
use App\Security\TokenSplitterInterface;
use App\Tests\Security\TokenSplitterMock;
use DateTimeImmutable;

/**
 * Tests the user registration repository.
 */
class UserRegistrationRepositoryTest extends RepositoryTestCase
{
    private UserRegistrationRepository $repository;

    public function testSaveAndRemove(): void
    {
        $now = new DateTimeImmutable('now');
        $registration = new UserRegistration('bob@bing.com', $now, 'a', 'b');
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
        $result = $this->repository->createUserRegistration('bob@gmail.com');
        $this->assertFalse($result->isFake());
    }

    public function testCreateIfOneExists(): void
    {
        $result = $this->repository->createUserRegistration('max@gmail.com');
        $this->assertFalse($result->isFake());
    }

    public function testCreateIfEmailIsRegistered(): void
    {
        $result = $this->repository->createUserRegistration('david@gmail.com');
        $this->assertTrue($result->isFake());
    }

    public function testCreateIfActiveAmountExceeded(): void
    {
        $result = $this->repository->createUserRegistration('roman@gmail.com');
        $this->assertTrue($result->isFake());
    }

    public function testCreateIfOneIsActiveAndOneIsTimeExpired(): void
    {
        $result = $this->repository->createUserRegistration('lucas@gmail.com');
        $this->assertFalse($result->isFake());
    }

    public function testCreateIfOneIsActiveAndOneIsDisabled(): void
    {
        $result = $this->repository->createUserRegistration('tim@gmail.com');
        $this->assertFalse($result->isFake());
    }

    public function testCreateIfOneIsActiveAndOneIsUsed(): void
    {
        $result = $this->repository->createUserRegistration('alena@gmail.com');
        $this->assertFalse($result->isFake());
    }

    public function testCreateIfSelectorExists(): void
    {
        $container = static::getContainer();

        /** @var TokenSplitterMock $splitterMock Configured in services_test.yaml */
        $splitterMock = $container->get(TokenSplitterInterface::class);
        $splitterMock
            ->addTestToken('max123') // max is the selector (this one exists in the test db)
            ->addTestToken('xyz123') // xyz is the selector (this one does not exist in the test db)
        ;

        $result = $this->repository->createUserRegistration('bob@gmail.com');
        $this->assertFalse($result->isFake());

        $userRegistration = $result->getUserRegistration();
        $this->assertSame('xyz', $userRegistration->getSelector());
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

    protected function setUp(): void
    {
        parent::setUp();

        /** @var UserRegistrationRepository $repository */
        $repository = $this->entityManager->getRepository(UserRegistration::class);
        $this->repository = $repository;
    }
}