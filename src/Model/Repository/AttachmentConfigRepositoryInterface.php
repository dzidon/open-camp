<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\AttachmentConfigSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\AttachmentConfig;
use Symfony\Component\Uid\UuidV4;

interface AttachmentConfigRepositoryInterface
{
    /**
     * Saves an attachment config.
     *
     * @param AttachmentConfig $attachmentConfig
     * @param bool $flush
     * @return void
     */
    public function saveAttachmentConfig(AttachmentConfig $attachmentConfig, bool $flush): void;

    /**
     * Removes an attachment config.
     *
     * @param AttachmentConfig $attachmentConfig
     * @param bool $flush
     * @return void
     */
    public function removeAttachmentConfig(AttachmentConfig $attachmentConfig, bool $flush): void;

    /**
     * Finds one attachment config by id.
     *
     * @param UuidV4 $id
     * @return AttachmentConfig|null
     */
    public function findOneById(UuidV4 $id): ?AttachmentConfig;

    /**
     * Finds one attachment config by name.
     *
     * @param string $name
     * @return AttachmentConfig|null
     */
    public function findOneByName(string $name): ?AttachmentConfig;

    /**
     * Returns admin attachment config search paginator.
     *
     * @param AttachmentConfigSearchData $data
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(AttachmentConfigSearchData $data, int $currentPage, int $pageSize): PaginatorInterface;
}