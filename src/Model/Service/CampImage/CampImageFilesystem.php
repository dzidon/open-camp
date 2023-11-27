<?php

namespace App\Model\Service\CampImage;

use App\Model\Entity\CampImage;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @inheritDoc
 */
class CampImageFilesystem implements CampImageFilesystemInterface
{
    private string $campImageDirectory;
    private string $noImagePath;
    private string $kernelProjectDirectory;

    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem,
                                string     $campImageDirectory,
                                string     $noImagePath,
                                string     $kernelProjectDirectory)
    {
        $this->filesystem = $filesystem;

        $this->campImageDirectory = $campImageDirectory;
        $this->kernelProjectDirectory = $kernelProjectDirectory;
        $this->noImagePath = $noImagePath;
    }

    /**
     * @inheritDoc
     */
    public function getFilePath(?CampImage $campImage): string
    {
        if ($campImage === null)
        {
            return $this->noImagePath;
        }

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