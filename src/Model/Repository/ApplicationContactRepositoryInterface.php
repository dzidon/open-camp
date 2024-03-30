<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\ApplicationContactSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationContact;
use Symfony\Component\Uid\UuidV4;

interface ApplicationContactRepositoryInterface
{
    /**
     * Saves an application contact.
     *
     * @param ApplicationContact $applicationContact
     * @param bool $flush
     * @return void
     */
    public function saveApplicationContact(ApplicationContact $applicationContact, bool $flush): void;

    /**
     * Removes an application contact.
     *
     * @param ApplicationContact $applicationContact
     * @param bool $flush
     * @return void
     */
    public function removeApplicationContact(ApplicationContact $applicationContact, bool $flush): void;

    /**
     * Finds one application contact by id.
     *
     * @param UuidV4 $id
     * @return ApplicationContact|null
     */
    public function findOneById(UuidV4 $id): ?ApplicationContact;

    /**
     * Returns admin application contact search paginator.
     *
     * @param ApplicationContactSearchData $data
     * @param Application $application
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(ApplicationContactSearchData $data,
                                      Application                  $application,
                                      int                          $currentPage,
                                      int                          $pageSize): PaginatorInterface;
}