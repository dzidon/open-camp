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
use Symfony\Component\Uid\UuidV4;

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

        $uid = new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $camper = $repository->findOneById($uid);

        $this->assertSame($uid->toRfc4122(), $camper->getId()->toRfc4122());
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

        $paginator = $repository->getAdminPaginator($data, 1, 5);
        $this->assertSame(5, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(5, $paginator->getPageSize());

        $emails = $this->getUserEmails($paginator->getCurrentPageItems());
        $this->assertSame(['mark@gmail.com', 'xena@gmail.com', 'jeff@gmail.com', 'kate@gmail.com', 'david@gmail.com'], $emails);
    }

    public function testGetAdminPaginatorSortByCreatedAtAsc(): void
    {
        $repository = $this->getUserRepository();

        $data = new UserSearchData();
        $data->setSortBy(UserSortEnum::CREATED_AT_ASC);

        $paginator = $repository->getAdminPaginator($data, 1, 5);
        $this->assertSame(5, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(5, $paginator->getPageSize());

        $emails = $this->getUserEmails($paginator->getCurrentPageItems());
        $this->assertSame(['david@gmail.com', 'kate@gmail.com', 'jeff@gmail.com', 'xena@gmail.com', 'mark@gmail.com'], $emails);
    }

    public function testGetAdminPaginatorSortByEmailAsc(): void
    {
        $repository = $this->getUserRepository();

        $data = new UserSearchData();
        $data->setSortBy(UserSortEnum::EMAIL_ASC);

        $paginator = $repository->getAdminPaginator($data, 1, 5);
        $this->assertSame(5, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(5, $paginator->getPageSize());

        $emails = $this->getUserEmails($paginator->getCurrentPageItems());
        $this->assertSame(['david@gmail.com', 'jeff@gmail.com', 'kate@gmail.com', 'mark@gmail.com', 'xena@gmail.com'], $emails);
    }

    public function testGetAdminPaginatorSortByEmailDesc(): void
    {
        $repository = $this->getUserRepository();

        $data = new UserSearchData();
        $data->setSortBy(UserSortEnum::EMAIL_DESC);

        $paginator = $repository->getAdminPaginator($data, 1, 5);
        $this->assertSame(5, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(5, $paginator->getPageSize());

        $emails = $this->getUserEmails($paginator->getCurrentPageItems());
        $this->assertSame(['xena@gmail.com', 'mark@gmail.com', 'kate@gmail.com', 'jeff@gmail.com', 'david@gmail.com'], $emails);
    }

    public function testGetAdminPaginatorSortByLastNameAsc(): void
    {
        $repository = $this->getUserRepository();

        $data = new UserSearchData();
        $data->setSortBy(UserSortEnum::NAME_LAST_ASC);

        $paginator = $repository->getAdminPaginator($data, 1, 5);
        $this->assertSame(5, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(5, $paginator->getPageSize());

        $emails = $this->getUserEmails($paginator->getCurrentPageItems());
        $this->assertSame(['jeff@gmail.com', 'kate@gmail.com', 'xena@gmail.com', 'david@gmail.com', 'mark@gmail.com'], $emails);
    }

    public function testGetAdminPaginatorSortByLastNameDesc(): void
    {
        $repository = $this->getUserRepository();

        $data = new UserSearchData();
        $data->setSortBy(UserSortEnum::NAME_LAST_DESC);

        $paginator = $repository->getAdminPaginator($data, 1, 5);
        $this->assertSame(5, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(5, $paginator->getPageSize());

        $emails = $this->getUserEmails($paginator->getCurrentPageItems());
        $this->assertSame(['mark@gmail.com', 'david@gmail.com', 'xena@gmail.com', 'kate@gmail.com', 'jeff@gmail.com'], $emails);
    }

    public function testGetAdminPaginatorSortByLastActiveAtDesc(): void
    {
        $repository = $this->getUserRepository();

        $data = new UserSearchData();
        $data->setSortBy(UserSortEnum::LAST_ACTIVE_AT_DESC);

        $paginator = $repository->getAdminPaginator($data, 1, 5);
        $this->assertSame(5, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(5, $paginator->getPageSize());

        $emails = $this->getUserEmails($paginator->getCurrentPageItems());
        $this->assertSame(['xena@gmail.com', 'jeff@gmail.com', 'kate@gmail.com', 'david@gmail.com', 'mark@gmail.com'], $emails);
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
            $names[] = $user->getNameFirst() . ' ' . $user->getNameLast();
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