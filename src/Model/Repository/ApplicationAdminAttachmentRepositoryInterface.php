<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\ApplicationAdminAttachmentSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationAdminAttachment;
use Symfony\Component\Uid\UuidV4;

interface ApplicationAdminAttachmentRepositoryInterface
{
    /**
     * Saves an application admin attachment.
     *
     * @param ApplicationAdminAttachment $applicationAdminAttachment
     * @param bool $flush
     * @return void
     */
    public function saveApplicationAdminAttachment(ApplicationAdminAttachment $applicationAdminAttachment, bool $flush): void;

    /**
     * Removes an application admin attachment.
     *
     * @param ApplicationAdminAttachment $applicationAdminAttachment
     * @param bool $flush
     * @return void
     */
    public function removeApplicationAdminAttachment(ApplicationAdminAttachment $applicationAdminAttachment, bool $flush): void;

    /**
     * Finds one application admin attachment by id.
     *
     * @param UuidV4 $id
     * @return ApplicationAdminAttachment|null
     */
    public function findOneById(UuidV4 $id): ?ApplicationAdminAttachment;

    /**
     * Returns application admin attachment search paginator.
     *
     * @param ApplicationAdminAttachmentSearchData $data
     * @param Application $application
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(ApplicationAdminAttachmentSearchData $data,
                                      Application                          $application,
                                      int                                  $currentPage,
                                      int                                  $pageSize): PaginatorInterface;
}