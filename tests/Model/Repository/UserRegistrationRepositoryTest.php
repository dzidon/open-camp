<?php

namespace App\Tests\Model\Repository;

use App\Model\Entity\UserRegistration;
use App\Model\Repository\UserRegistrationRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests the UserRegistration repository.
 */
class UserRegistrationRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $repository = $this->getUserRegistrationRepository();

        $now = new DateTimeImmutable('now');
        $registration = new UserRegistration('bob@bing.com', $now, 'bob', '123');
        $repository->saveUserRegistration($registration, true);

        $loadedRegistration = $repository->findOneBySelector('bob');
        $this->assertNotNull($loadedRegistration);
        $this->assertSame($registration->getId(), $loadedRegistration->getId());

        $repository->removeUserRegistration($registration, true);
        $loadedRegistration = $repository->findOneBySelector('bob');
        $this->assertNull($loadedRegistration);
    }

    public function testSelectorExists(): void
    {
        $repository = $this->getUserRegistrationRepository();

        $this->assertFalse($repository->selectorExists('bob'));
        $this->assertTrue($repository->selectorExists('max'));
    }

    public function testFindOneBySelector(): void
    {
        $repository = $this->getUserRegistrationRepository();

        $registration = $repository->findOneBySelector('bob');
        $this->assertNull($registration);

        $registration = $repository->findOneBySelector('max');
        $this->assertNotNull($registration);
        $this->assertSame('max', $registration->getSelector());

        $registration = $repository->findOneBySelector('lu2');
        $this->assertNotNull($registration);

        $registration = $repository->findOneBySelector('ti2');
        $this->assertNotNull($registration);

        $registration = $repository->findOneBySelector('al2');
        $this->assertNotNull($registration);
    }

    public function testFindOneBySelectorActive(): void
    {
        $repository = $this->getUserRegistrationRepository();

        $registration = $repository->findOneBySelector('max', true);
        $this->assertNotNull($registration);

        $registration = $repository->findOneBySelector('lu2', true);
        $this->assertNull($registration);

        $registration = $repository->findOneBySelector('ti2', true);
        $this->assertNull($registration);

        $registration = $repository->findOneBySelector('al2', true);
        $this->assertNull($registration);
    }

    public function testFindOneBySelectorInactive(): void
    {
        $repository = $this->getUserRegistrationRepository();

        $registration = $repository->findOneBySelector('max', false);
        $this->assertNull($registration);

        $registration = $repository->findOneBySelector('lu2', false);
        $this->assertNotNull($registration);

        $registration = $repository->findOneBySelector('ti2', false);
        $this->assertNotNull($registration);

        $registration = $repository->findOneBySelector('al2', false);
        $this->assertNotNull($registration);
    }

    public function testFindByEmail(): void
    {
        $repository = $this->getUserRegistrationRepository();

        // bob
        $registrations = $repository->findByEmail('bob@gmail.com');
        $this->assertCount(0, $registrations);

        // lucas
        $registrations = $repository->findByEmail('lucas@gmail.com');
        $this->assertCount(2, $registrations);

        $selectors = $this->getSelectorsFromCollection($registrations);
        $this->assertContains('lu1', $selectors);
        $this->assertContains('lu2', $selectors);

        // tim
        $registrations = $repository->findByEmail('tim@gmail.com');
        $this->assertCount(2, $registrations);

        $selectors = $this->getSelectorsFromCollection($registrations);
        $this->assertContains('ti1', $selectors);
        $this->assertContains('ti2', $selectors);

        // alena
        $registrations = $repository->findByEmail('alena@gmail.com');
        $this->assertCount(2, $registrations);

        $selectors = $this->getSelectorsFromCollection($registrations);
        $this->assertContains('al1', $selectors);
        $this->assertContains('al2', $selectors);
    }

    public function testFindByEmailActive(): void
    {
        $repository = $this->getUserRegistrationRepository();

        // lucas
        $registrations = $repository->findByEmail('lucas@gmail.com', true);
        $this->assertCount(1, $registrations);

        $selectors = $this->getSelectorsFromCollection($registrations);
        $this->assertContains('lu1', $selectors);

        // tim
        $registrations = $repository->findByEmail('tim@gmail.com', true);
        $this->assertCount(1, $registrations);

        $selectors = $this->getSelectorsFromCollection($registrations);
        $this->assertContains('ti1', $selectors);

        // alena
        $registrations = $repository->findByEmail('alena@gmail.com', true);
        $this->assertCount(1, $registrations);

        $selectors = $this->getSelectorsFromCollection($registrations);
        $this->assertContains('al1', $selectors);
    }

    public function testFindByEmailInactive(): void
    {
        $repository = $this->getUserRegistrationRepository();

        // lucas
        $registrations = $repository->findByEmail('lucas@gmail.com', false);
        $this->assertCount(1, $registrations);

        $selectors = $this->getSelectorsFromCollection($registrations);
        $this->assertContains('lu2', $selectors);

        // tim
        $registrations = $repository->findByEmail('tim@gmail.com', false);
        $this->assertCount(1, $registrations);

        $selectors = $this->getSelectorsFromCollection($registrations);
        $this->assertContains('ti2', $selectors);

        // alena
        $registrations = $repository->findByEmail('alena@gmail.com', false);
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

    private function getUserRegistrationRepository(): UserRegistrationRepository
    {
        $container = static::getContainer();

        /** @var UserRegistrationRepository $repository */
        $repository = $container->get(UserRegistrationRepository::class);

        return $repository;
    }
}