<?php

namespace App\Model\Repository;

use App\Library\Data\User\ContactSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\Contact;
use App\Model\Entity\User;
use Symfony\Component\Uid\UuidV4;

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
     * Finds one contact by id.
     *
     * @param UuidV4 $id
     * @return Contact|null
     */
    public function findOneById(UuidV4 $id): ?Contact;

    /**
     * Returns user contact search paginator.
     *
     * @param ContactSearchData $data
     * @param User $user
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getUserPaginator(ContactSearchData $data, User $user, int $currentPage, int $pageSize): PaginatorInterface;
}