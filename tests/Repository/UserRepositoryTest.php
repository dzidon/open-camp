<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Tests the user repository.
 */
class UserRepositoryTest extends RepositoryTestCase
{
    private UserRepository $repository;

    public function testSaveAndRemove(): void
    {
        $user = new User('bob@bing.com');
        $this->repository->saveUser($user, true);
        $id = $user->getId();

        $loadedUser = $this->repository->find($id);
        $this->assertNotNull($loadedUser);
        $this->assertSame($user->getId(), $loadedUser->getId());

        $this->repository->removeUser($user, true);
        $loadedUser = $this->repository->find($id);
        $this->assertNull($loadedUser);
    }

    public function testCreate(): void
    {
        $user = $this->repository->createUser('bob@bing.com');
        $this->assertNotNull($user);
        $this->assertSame('bob@bing.com', $user->getEmail());
        $this->assertNull($user->getPassword());

        $user = $this->repository->createUser('alice@bing.com', '123456');
        $this->assertNotNull($user);
        $this->assertSame('alice@bing.com', $user->getEmail());
        $this->assertNotNull($user->getPassword());
        $this->assertNotSame('123456', $user->getPassword());
    }

    public function testFindOneByEmail(): void
    {
        $loadedUser = $this->repository->findOneByEmail('bob@bing.com');
        $this->assertNull($loadedUser);

        $loadedUser = $this->repository->findOneByEmail('david@gmail.com');
        $this->assertNotNull($loadedUser);
        $this->assertSame('david@gmail.com', $loadedUser->getEmail());
    }

    public function testIsEmailRegistered(): void
    {
        $registered = $this->repository->isEmailRegistered('david@gmail.com');
        $this->assertTrue($registered);

        $registered = $this->repository->isEmailRegistered('non-existent-user@gmail.com');
        $this->assertFalse($registered);
    }

    public function testSupportsClass(): void
    {
        $this->assertTrue($this->repository->supportsClass(User::class));
    }

    public function testLoadUserByIdentifier(): void
    {
        /** @var User $loadedUser */
        $loadedUser = $this->repository->loadUserByIdentifier('david@gmail.com');
        $this->assertNotNull($loadedUser);
        $this->assertSame('david@gmail.com', $loadedUser->getEmail());

        $this->expectException(UserNotFoundException::class);
        $this->repository->loadUserByIdentifier('bob@bing.com');
    }

    public function testRefreshUser(): void
    {
        /** @var User $loadedUser */
        $loadedUser = $this->repository->findOneBy(['email' => 'david@gmail.com']);
        $this->assertNotNull($loadedUser);

        /** @var User $refreshedUser */
        $refreshedUser = $this->repository->refreshUser($loadedUser);
        $this->assertSame($loadedUser->getId(), $refreshedUser->getId());

        $newUser = new User('bob@bing.com');

        $this->expectException(UserNotFoundException::class);
        $this->repository->refreshUser($newUser);

        /** @var UserInterface|MockObject $unsupportedUser */
        $unsupportedUser = $this->getMockBuilder(UserInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->expectException(UnsupportedUserException::class);
        $this->repository->refreshUser($unsupportedUser);
    }

    protected function setUp(): void
    {
        parent::setUp();

        /** @var UserRepository $repository */
        $repository = $this->entityManager->getRepository(User::class);
        $this->repository = $repository;
    }
}