<?php

namespace App\Model\Service\CampImage;

use App\Model\Entity\CampImage;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @inheritDoc
 */
class CampImageFilesystem implements CampImageFilesystemInterface
{
    private FilesystemOperator $campImageStorage;

    private string $campImagePublicPathPrefix;

    private string $campImageDirectory;

    private string $noImagePath;

    public function __construct(
        FilesystemOperator $campImageStorage,

        #[Autowire('%app.camp_image_public_path_prefix%')]
        string $campImagePublicPathPrefix,

        #[Autowire('%app.camp_image_directory%')]
        string $campImageDirectory,

        #[Autowire('%app.no_camp_image_path%')]
        string $noImagePath
    ) {
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
        $fileName = $campImage->getFileName();

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

        $fileName = $campImage->getFileName();

        if (!$this->campImageStorage->has($fileName))
        {
            return $noImageUrl;
        }

        return $this->campImagePublicPathPrefix . $this->campImageDirectory . '/' . $fileName;
    }

    /**
     * @inheritDoc
     */
    public function removeFile(CampImage $campImage): void
    {
        $fileName = $campImage->getFileName();
        $this->campImageStorage->delete($fileName);
    }

    private function getNoImageUrl(): string
    {
        return $this->campImagePublicPathPrefix . $this->noImagePath;
    }
}