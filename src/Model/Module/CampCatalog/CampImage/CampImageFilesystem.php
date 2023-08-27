<?php

namespace App\Model\Module\CampCatalog\CampImage;

use App\Model\Entity\CampImage;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @inheritDoc
 */
class CampImageFilesystem implements CampImageFilesystemInterface
{
    private string $campImageDirectory;
    private string $kernelProjectDirectory;

    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem, string $campImageDirectory, string $kernelProjectDirectory)
    {
        $this->filesystem = $filesystem;

        $this->campImageDirectory = $campImageDirectory;
        $this->kernelProjectDirectory = $kernelProjectDirectory;
    }

    /**
     * @inheritDoc
     */
    public function getFilePath(CampImage $campImage): string
    {
        $id = $campImage->getId();

        return $this->campImageDirectory . '/' . $id->toRfc4122() . '.' . $campImage->getExtension();
    }

    /**
     * @inheritDoc
     */
    public function removeFile(CampImage $campImage): void
    {
        $filePath = $this->kernelProjectDirectory . '/public/' . $this->getFilePath($campImage);
        $this->filesystem->remove($filePath);
    }
}