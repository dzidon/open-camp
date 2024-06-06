<?php

namespace App\Model\Service\DownloadableFile;

use App\Model\Entity\DownloadableFile;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @inheritDoc
 */
class DownloadableFileFactory implements DownloadableFileFactoryInterface
{
    private FilesystemOperator $downloadableFileStorage;

    public function __construct(FilesystemOperator $downloadableFileStorage)
    {
        $this->downloadableFileStorage = $downloadableFileStorage;
    }

    /**
     * @inheritDoc
     */
    public function createDownloadableFile(File $file, string $title, int $priority): DownloadableFile
    {
        $extension = $file->guessExtension();
        $downloadableFile = new DownloadableFile($title, $extension, $priority);
        $newFileName = $downloadableFile->getFileName();
        $contents = $file->getContent();

        $this->downloadableFileStorage->write($newFileName, $contents);

        return $downloadableFile;
    }
}