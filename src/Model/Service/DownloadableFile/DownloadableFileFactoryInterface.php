<?php

namespace App\Model\Service\DownloadableFile;

use App\Model\Entity\DownloadableFile;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Creates downloadable file entities.
 */
interface DownloadableFileFactoryInterface
{
    /**
     * Creates a downloadable file entity for the given file.
     *
     * @param File $file
     * @param string $title
     * @param int $priority
     * @return DownloadableFile
     */
    public function createDownloadableFile(File $file, string $title, int $priority): DownloadableFile;
}