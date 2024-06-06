<?php

namespace App\Model\Service\DownloadableFile;

use App\Model\Entity\DownloadableFile;

/**
 * Helper service for downloadable files.
 */
interface DownloadableFileFilesystemInterface
{
    /**
     * Returns the contents of the given downloadable file.
     *
     * @param DownloadableFile $downloadableFile
     * @return string|null
     */
    public function getFileContents(DownloadableFile $downloadableFile): ?string;

    /**
     * Removes the given downloadable file.
     *
     * @param DownloadableFile $downloadableFile
     * @return void
     */
    public function removeFile(DownloadableFile $downloadableFile): void;
}