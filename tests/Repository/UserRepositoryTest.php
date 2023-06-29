<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Tests the User repository.
 */
class UserRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $repository = $this->getUserRepository();

        $user = new User('bob@bing.com');
        $repository->saveUser($user, true);
        $id = $user->getId();

        $loadedUser = $repository->find($id);
        $this->assertNotNull($loadedUser);
        $this->assertSame($user->getId(), $loadedUser->getId());

        $repository->removeUser($user, true);
        $loadedUser = $repository->find($id);
        $this->assertNull($loadedUser);
    }

    public function testCreate(): void
    {
        $repository = $this->getUserRepository();

        $user = $repository->createUser('bob@bing.com');
        $this->assertNotNull($user);
        $this->assertSame('bob@bing.com', $user->getEmail());
        $this->assertNull($user->getPassword());

        $hasher = $this->getUserPasswordHasher();
        $user = $repository->createUser('alice@bing.com', '123456');
        $this->assertNotNull($user);
        $this->assertSame('alice@bing.com', $user->getEmail());
        $this->assertNotNull($user->getPassword());
        $this->assertTrue($hasher->isPasswordValid($user, '123456'));
    }

    public function testFindOneByEmail(): void
    {
        $repository = $this->getUserRepository();

        $loadedUser = $repository->findOneByEmail('bob@bing.com');
        $this->assertNull($loadedUser);

        $loadedUser = $repository->findOneByEmail('david@gmail.com');
        $this->assertNotNull($loadedUser);
        $this->assertSame('david@gmail.com', $loadedUser->getEmail());
    }

    public function testIsEmailRegistered(): void
    {
        $repository = $this->getUserRepository();

        $registered = $repository->isEmailRegistered('david@gmail.com');
        $this->assertTrue($registered);

        $registered = $repository->isEmailRegistered('non-existent-user@gmail.com');
        $this->assertFalse($registered);
    }

    public function testFindByRole(): void
    {
        $repository = $this->getUserRepository();

        $user = $repository->findOneByEmail('david@gmail.com');
        $role = $user->getRole();

        $usersLoadedByRole = $repository->findByRole($role);
        $this->assertContains($user, $usersLoadedByRole);
    }

    public function testSupportsClass(): void
    {
        $repository = $this->getUserRepository();
        $this->assertTrue($repository->supportsClass(User::class));
    }

    public function testLoadUserByIdentifier(): void
    {
        $repository = $this->getUserRepository();

        /** @var User $loadedUser */
        $loadedUser = $repository->loadUserByIdentifier('david@gmail.com');
        $this->assertNotNull($loadedUser);
        $this->assertSame('david@gmail.com', $loadedUser->getEmail());

        $this->expectException(UserNotFoundException::class);
        $repository->loadUserByIdentifier('bob@bing.com');
    }

    public function testRefreshUser(): void
    {
        $repository = $this->getUserRepository();

        /** @var User $loadedUser */
        $loadedUser = $repository->findOneBy(['email' => 'david@gmail.com']);
        $this->assertNotNull($loadedUser);

        /** @var User $refreshedUser */
        $refreshedUser = $repository->refreshUser($loadedUser);
        $this->assertSame($loadedUser->getId(), $refreshedUser->getId());

        $newUser = new User('bob@bing.com');

        $this->expectException(UserNotFoundException::class);
        $repository->refreshUser($newUser);

        /** @var UserInterface|MockObject $unsupportedUser */
        $unsupportedUser = $this->getMockBuilder(UserInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->expectException(UnsupportedUserException::class);
        $repository->refreshUser($unsupportedUser);
    }

    private function getUserPasswordHasher(): UserPasswordHasherInterface
    {
        $container = static::getContainer();

        /** @var UserPasswordHasherInterface $hasher */
        $hasher = $container->get(UserPasswordHasherInterface::class);

        return $hasher;
    }

    private function getUserRepository(): UserRepository
    {
        $container = static::getContainer();

        /** @var UserRepository $repository */
        $repository = $container->get(UserRepository::class);

        return $repository;
    }
}