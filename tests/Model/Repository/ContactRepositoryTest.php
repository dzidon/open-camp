<?php

namespace App\Tests\Model\Repository;

use App\Library\Data\User\ContactSearchData;
use App\Library\Enum\Search\Data\User\ContactSortEnum;
use App\Model\Entity\Contact;
use App\Model\Entity\User;
use App\Model\Enum\Entity\ContactRoleEnum;
use App\Model\Repository\ContactRepository;
use App\Model\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

/**
 * Tests the Contact repository.
 */
class ContactRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $contactRepository = $this->getContactRepository();
        $userRepository = $this->getUserRepository();

        $user = new User('bob@bing.com');
        $contact = new Contact('Bob', 'Bobby', ContactRoleEnum::MOTHER, $user);

        $userRepository->saveUser($user, false);
        $contactRepository->saveContact($contact, true);
        $id = $contact->getId();

        $loadedContact = $contactRepository->findOneById($id);
        $this->assertNotNull($loadedContact);
        $this->assertSame($id, $loadedContact->getId());

        $contactRepository->removeContact($contact, true);
        $loadedContact = $contactRepository->findOneById($id);
        $this->assertNull($loadedContact);
    }

    public function testFindOneById(): void
    {
        $repository = $this->getContactRepository();

        $uid = new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $camper = $repository->findOneById($uid);

        $this->assertSame($uid->toRfc4122(), $camper->getId()->toRfc4122());
    }

    public function testGetUserPaginator(): void
    {
        $contactRepository = $this->getContactRepository();
        $userRepository = $this->getUserRepository();
        $user = $userRepository->findOneByEmail('david@gmail.com');

        $data = new ContactSearchData();
        $paginator = $contactRepository->getUserPaginator($data, $user, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getContactNames($paginator->getCurrentPageItems());
        $this->assertSame(['Jessica Smith', 'David Smith'], $names);
    }

    public function testGetAdminPaginatorWithPhrase(): void
    {
        $contactRepository = $this->getContactRepository();
        $userRepository = $this->getUserRepository();
        $user = $userRepository->findOneByEmail('david@gmail.com');

        $data = new ContactSearchData();
        $data->setPhrase('david');
        $paginator = $contactRepository->getUserPaginator($data, $user, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getContactNames($paginator->getCurrentPageItems());
        $this->assertSame(['David Smith'], $names);
    }

    public function testGetUserPaginatorSortByCreatedAtDesc(): void
    {
        $contactRepository = $this->getContactRepository();
        $userRepository = $this->getUserRepository();
        $user = $userRepository->findOneByEmail('david@gmail.com');

        $data = new ContactSearchData();
        $data->setSortBy(ContactSortEnum::CREATED_AT_DESC);

        $paginator = $contactRepository->getUserPaginator($data, $user, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getContactNames($paginator->getCurrentPageItems());
        $this->assertSame(['Jessica Smith', 'David Smith'], $names);
    }

    public function testGetUserPaginatorSortByCreatedAtAsc(): void
    {
        $contactRepository = $this->getContactRepository();
        $userRepository = $this->getUserRepository();
        $user = $userRepository->findOneByEmail('david@gmail.com');

        $data = new ContactSearchData();
        $data->setSortBy(ContactSortEnum::CREATED_AT_ASC);

        $paginator = $contactRepository->getUserPaginator($data, $user, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getContactNames($paginator->getCurrentPageItems());
        $this->assertSame(['David Smith', 'Jessica Smith'], $names);
    }

    private function getContactNames(array $contacts): array
    {
        $names = [];

        /** @var Contact $contact */
        foreach ($contacts as $contact)
        {
            $names[] = $contact->getNameFirst() . ' ' . $contact->getNameLast();
        }

        return $names;
    }

    private function getUserRepository(): UserRepositoryInterface
    {
        $container = static::getContainer();

        /** @var UserRepositoryInterface $repository */
        $repository = $container->get(UserRepositoryInterface::class);

        return $repository;
    }

    private function getContactRepository(): ContactRepository
    {
        $container = static::getContainer();

        /** @var ContactRepository $repository */
        $repository = $container->get(ContactRepository::class);

        return $repository;
    }
}