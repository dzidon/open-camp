<?php

namespace App\Model\Service\DownloadableFile;

use App\Model\Entity\DownloadableFile;
use League\Flysystem\FilesystemOperator;

/**
 * @inheritDoc
 */
class DownloadableFileFilesystem implements DownloadableFileFilesystemInterface
{
    private FilesystemOperator $downloadableFileStorage;

    public function __construct(FilesystemOperator $downloadableFileStorage)
    {
        $this->downloadableFileStorage = $downloadableFileStorage;
    }

    /**
     * @inheritDoc
     */
    public function getFileContents(DownloadableFile $downloadableFile): ?string
    {
        $fileName = $downloadableFile->getFileName();

        if (!$this->downloadableFileStorage->has($fileName))
        {
            return null;
        }

        return $this->downloadableFileStorage->read($fileName);
    }

    /**
     * @inheritDoc
     */
    public function removeFile(DownloadableFile $downloadableFile): void
    {
        $fileName = $downloadableFile->getFileName();
        $this->downloadableFileStorage->delete($fileName);
    }
}