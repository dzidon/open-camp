<?php

namespace App\Model\Service\CampImage;

use App\Model\Entity\CampImage;
use League\Flysystem\FilesystemOperator;

/**
 * @inheritDoc
 */
class CampImageFilesystem implements CampImageFilesystemInterface
{
    private FilesystemOperator $campImageStorage;

    private string $campImagePublicPathPrefix;

    private string $campImageDirectory;

    private string $noImagePath;

    public function __construct(FilesystemOperator $campImageStorage,
                                string             $campImagePublicPathPrefix,
                                string             $campImageDirectory,
                                string             $noImagePath)
    {
        $this->campImageStorage = $campImageStorage;

        $this->campImagePublicPathPrefix = $campImagePublicPathPrefix;
        $this->campImageDirectory = $campImageDirectory;
        $this->noImagePath = $noImagePath;
    }

    /**
     * @inheritDoc
     */
    public function getImageLastModified(CampImage $campImage): ?int
    {
        $fileName = $this->getCampImageName($campImage);

        if (!$this->campImageStorage->has($fileName))
        {
            return null;
        }

        return $this->campImageStorage->lastModified($fileName);
    }

    /**
     * @inheritDoc
     */
    public function isUrlPlaceholder(string $publicUrl): bool
    {
        return explode('?', $publicUrl)[0] === $this->getNoImageUrl();
    }

    /**
     * @inheritDoc
     */
    public function getImagePublicUrl(?CampImage $campImage): string
    {
        $noImageUrl = $this->getNoImageUrl();

        if ($campImage === null)
        {
            return $noImageUrl;
        }

        $fileName = $this->getCampImageName($campImage);

        if (!$this->campImageStorage->has($fileName))
        {
            return $noImageUrl;
        }

        $fileName = $this->getCampImageName($campImage);

        return $this->campImagePublicPathPrefix . $this->campImageDirectory . '/' . $fileName;
    }

    /**
     * @inheritDoc
     */
    public function removeFile(CampImage $campImage): void
    {
        $fileName = $this->getCampImageName($campImage);
        $this->campImageStorage->delete($fileName);
    }

    private function getCampImageName(CampImage $campImage): string
    {
        $campImageId = $campImage->getId();

        return $campImageId->toRfc4122() . '.' . $campImage->getExtension();
    }

    private function getNoImageUrl(): string
    {
        return $this->campImagePublicPathPrefix . $this->noImagePath;
    }
}