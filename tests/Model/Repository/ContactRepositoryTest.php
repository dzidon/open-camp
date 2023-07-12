<?php

namespace App\Tests\Model\Repository;

use App\Enum\Search\Data\User\ContactSortEnum;
use App\Form\DataTransfer\Data\User\ContactSearchData;
use App\Model\Entity\Contact;
use App\Model\Entity\User;
use App\Model\Repository\ContactRepository;
use App\Model\Repository\UserRepositoryInterface;
use libphonenumber\PhoneNumber;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

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
        $contact = new Contact('Bob Bobby', 'bob@bing.com', new PhoneNumber(), $user);

        $userRepository->saveUser($user, false);
        $contactRepository->saveContact($contact, true);
        $id = $contact->getId();

        $loadedContact = $contactRepository->find($id);
        $this->assertNotNull($loadedContact);
        $this->assertSame($contact->getId(), $loadedContact->getId());

        $contactRepository->removeContact($contact, true);
        $loadedContact = $contactRepository->find($id);
        $this->assertNull($loadedContact);
    }

    public function testCreate(): void
    {
        $repository = $this->getContactRepository();

        $phoneNumber = new PhoneNumber();
        $user = new User('bob@bing.com');
        $contact = $repository->createContact('Bob Bobby', 'bob@bing.com', $phoneNumber, $user);
        $this->assertSame('Bob Bobby', $contact->getName());
        $this->assertSame('bob@bing.com', $contact->getEmail());
        $this->assertSame($phoneNumber, $contact->getPhoneNumber());
        $this->assertSame($user, $contact->getUser());

        $user = new User('bob@bing.com');
        $contact = $repository->createContact('Bob Bobby', 'bob@bing.com', '+420607555666', $user);
        $this->assertSame('Bob Bobby', $contact->getName());
        $this->assertSame('bob@bing.com', $contact->getEmail());
        $this->assertSame($user, $contact->getUser());

        $phoneNumber = $contact->getPhoneNumber();
        $this->assertSame(420, $phoneNumber->getCountryCode());
        $this->assertSame('607555666', $phoneNumber->getNationalNumber());
    }

    public function testFindOneById(): void
    {
        $contactRepository = $this->getContactRepository();
        $userRepository = $this->getUserRepository();

        $loadedContact = $contactRepository->findOneById(-10000);
        $this->assertNull($loadedContact);

        $phoneNumber = (new PhoneNumber())
            ->setNationalNumber(420)
            ->setNationalNumber('607555666')
        ;

        $user = new User('bob@bing.com');
        $contact = new Contact('Bob Bobby', 'bob@bing.com', $phoneNumber, $user);
        $userRepository->saveUser($user, false);
        $contactRepository->saveContact($contact, true);

        $loadedContact = $contactRepository->findOneById($contact->getId());
        $this->assertSame($contact->getId(), $loadedContact->getId());
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

    public function testGetUserPaginatorSortByIdDesc(): void
    {
        $contactRepository = $this->getContactRepository();
        $userRepository = $this->getUserRepository();
        $user = $userRepository->findOneByEmail('david@gmail.com');

        $data = new ContactSearchData();
        $data->setSortBy(ContactSortEnum::ID_DESC);

        $paginator = $contactRepository->getUserPaginator($data, $user, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getContactNames($paginator->getCurrentPageItems());
        $this->assertSame(['Jessica Smith', 'David Smith'], $names);
    }

    public function testGetUserPaginatorSortByIdAsc(): void
    {
        $contactRepository = $this->getContactRepository();
        $userRepository = $this->getUserRepository();
        $user = $userRepository->findOneByEmail('david@gmail.com');

        $data = new ContactSearchData();
        $data->setSortBy(ContactSortEnum::ID_ASC);

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
            $names[] = $contact->getName();
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