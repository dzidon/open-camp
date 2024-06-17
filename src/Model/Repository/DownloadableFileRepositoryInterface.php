<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\DownloadableFileSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\DownloadableFile;
use Symfony\Component\Uid\UuidV4;

interface DownloadableFileRepositoryInterface
{
    /**
     * Saves a downloadable file.
     *
     * @param DownloadableFile $downloadableFile
     * @param bool $flush
     * @return void
     */
    public function saveDownloadableFile(DownloadableFile $downloadableFile, bool $flush): void;

    /**
     * Removes a downloadable file.
     *
     * @param DownloadableFile $downloadableFile
     * @param bool $flush
     * @return void
     */
    public function removeDownloadableFile(DownloadableFile $downloadableFile, bool $flush): void;

    /**
     * Finds one downloadable file by id.
     *
     * @param UuidV4 $id
     * @return DownloadableFile|null
     */
    public function findOneById(UuidV4 $id): ?DownloadableFile;

    /**
     * Finds all extensions that are in use.
     *
     * @return string[]
     */
    public function findUsedExtensions(): array;

    /**
     * Returns true if there is at least one downloadable file.
     *
     * @return bool
     */
    public function existsAtLeastOne(): bool;

    /**
     * Returns user downloadable file search paginator.
     *
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getUserPaginator(int $currentPage, int $pageSize): PaginatorInterface;

    /**
     * Returns admin downloadable file search paginator.
     *
     * @param DownloadableFileSearchData $data
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(DownloadableFileSearchData $data, int $currentPage, int $pageSize): PaginatorInterface;
}