<?php

namespace App\Model\Repository;

use App\Form\DataTransfer\Data\User\ContactSearchDataInterface;
use App\Model\Entity\Contact;
use App\Model\Entity\User;
use App\Search\Paginator\PaginatorInterface;
use libphonenumber\PhoneNumber;

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
     * @param string $name
     * @param string $email
     * @param string|PhoneNumber $phoneNumber
     * @param User $user
     * @return Contact
     */
    public function createContact(string $name, string $email, string|PhoneNumber $phoneNumber, User $user): Contact;

    /**
     * Finds one contact by id.
     *
     * @param int $id
     * @return Contact|null
     */
    public function findOneById(int $id): ?Contact;

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