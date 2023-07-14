<?php

namespace App\Tests\Model\Repository;

use App\Enum\Search\Data\Admin\UserSortEnum;
use App\Form\DataTransfer\Data\Admin\UserSearchData;
use App\Model\Entity\User;
use App\Model\Repository\UserRepository;
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

    public function testFindOneById(): void
    {
        $repository = $this->getUserRepository();

        $loadedUser = $repository->findOneById(-10000);
        $this->assertNull($loadedUser);

        $user = new User('New role');
        $repository->saveUser($user, true);

        $loadedUser = $repository->findOneById($user->getId());
        $this->assertSame($user->getId(), $loadedUser->getId());
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

    public function testGetAdminPaginator(): void
    {
        $repository = $this->getUserRepository();

        $data = new UserSearchData();
        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(5, $paginator->getTotalItems());
        $this->assertSame(3, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $emails = $this->getUserEmails($paginator->getCurrentPageItems());
        $this->assertSame(['mark@gmail.com', 'xena@gmail.com'], $emails);
    }

    public function testGetAdminPaginatorWithEmail(): void
    {
        $repository = $this->getUserRepository();

        $data = new UserSearchData();
        $data->setPhrase('xena');

        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $emails = $this->getUserEmails($paginator->getCurrentPageItems());
        $this->assertSame(['xena@gmail.com'], $emails);
    }

    public function testGetAdminPaginatorWithName(): void
    {
        $repository = $this->getUserRepository();

        $data = new UserSearchData();
        $data->setPhrase('David Smi');

        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $emails = $this->getUserNames($paginator->getCurrentPageItems());
        $this->assertSame(['David Smith'], $emails);
    }

    public function testGetAdminPaginatorSortByCreatedAtDesc(): void
    {
        $repository = $this->getUserRepository();

        $data = new UserSearchData();
        $data->setSortBy(UserSortEnum::CREATED_AT_DESC);

        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(5, $paginator->getTotalItems());
        $this->assertSame(3, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $emails = $this->getUserEmails($paginator->getCurrentPageItems());
        $this->assertSame(['mark@gmail.com', 'xena@gmail.com'], $emails);
    }

    public function testGetAdminPaginatorSortByCreatedAtAsc(): void
    {
        $repository = $this->getUserRepository();

        $data = new UserSearchData();
        $data->setSortBy(UserSortEnum::CREATED_AT_ASC);

        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(5, $paginator->getTotalItems());
        $this->assertSame(3, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $emails = $this->getUserEmails($paginator->getCurrentPageItems());
        $this->assertSame(['david@gmail.com', 'kate@gmail.com'], $emails);
    }

    public function testGetAdminPaginatorSortByEmailAsc(): void
    {
        $repository = $this->getUserRepository();

        $data = new UserSearchData();
        $data->setSortBy(UserSortEnum::EMAIL_ASC);

        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(5, $paginator->getTotalItems());
        $this->assertSame(3, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $emails = $this->getUserEmails($paginator->getCurrentPageItems());
        $this->assertSame(['david@gmail.com', 'jeff@gmail.com'], $emails);
    }

    public function testGetAdminPaginatorSortByEmailDesc(): void
    {
        $repository = $this->getUserRepository();

        $data = new UserSearchData();
        $data->setSortBy(UserSortEnum::EMAIL_DESC);

        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(5, $paginator->getTotalItems());
        $this->assertSame(3, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $emails = $this->getUserEmails($paginator->getCurrentPageItems());
        $this->assertSame(['xena@gmail.com', 'mark@gmail.com'], $emails);
    }

    public function testGetAdminPaginatorWithRole(): void
    {
        $userRepository = $this->getUserRepository();
        $role = $userRepository
            ->findOneByEmail('david@gmail.com')
            ->getRole()
        ;

        $data = new UserSearchData();
        $data->setRole($role);

        $paginator = $userRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $emails = $this->getUserEmails($paginator->getCurrentPageItems());
        $this->assertSame(['david@gmail.com'], $emails);
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

    private function getUserEmails(array $users): array
    {
        $emails = [];

        /** @var User $user */
        foreach ($users as $user)
        {
            $emails[] = $user->getEmail();
        }

        return $emails;
    }

    private function getUserNames(array $users): array
    {
        $names = [];

        /** @var User $user */
        foreach ($users as $user)
        {
            $names[] = (string) $user->getName();
        }

        return $names;
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