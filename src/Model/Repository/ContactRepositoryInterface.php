<?php

namespace App\Model\Repository;

use App\Enum\Entity\ContactRoleEnum;
use App\Form\DataTransfer\Data\User\ContactSearchDataInterface;
use App\Model\Entity\Contact;
use App\Model\Entity\User;
use App\Search\Paginator\PaginatorInterface;
use libphonenumber\PhoneNumber;
use Symfony\Component\Uid\UuidV4;

/**
 * Contact CRUD.
 */
interface ContactRepositoryInterface
{
    /**
     * Saves a contact.
     *
     * @param Contact $contact
     * @param bool $flush
     * @return void
     */
    public function saveContact(Contact $contact, bool $flush): void;

    /**
     * Removes a contact.
     *
     * @param Contact $contact
     * @param bool $flush
     * @return void
     */
    public function removeContact(Contact $contact, bool $flush): void;

    /**
     * Creates a contact.
     *
     * @param string $nameFirst
     * @param string $nameLast
     * @param ContactRoleEnum $role
     * @param User $user
     * @return Contact
     */
    public function createContact(string $nameFirst, string $nameLast, ContactRoleEnum $role, User $user): Contact;

    /**
     * Finds one contact by id.
     *
     * @param UuidV4 $id
     * @return Contact|null
     */
    public function findOneById(UuidV4 $id): ?Contact;

    /**
     * Returns user contact search paginator.
     *
     * @param ContactSearchDataInterface $data
     * @param User $user
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getUserPaginator(ContactSearchDataInterface $data, User $user, int $currentPage, int $pageSize): PaginatorInterface;
}