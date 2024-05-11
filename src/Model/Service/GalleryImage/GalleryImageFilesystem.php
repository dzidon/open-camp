<?php

namespace App\Model\Service\GalleryImage;

use App\Model\Entity\GalleryImage;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @inheritDoc
 */
class GalleryImageFilesystem implements GalleryImageFilesystemInterface
{
    private FilesystemOperator $galleryImageStorage;

    private string $galleryImagePublicPathPrefix;

    private string $galleryImageDirectory;

    private string $noImagePath;

    public function __construct(
        FilesystemOperator $galleryImageStorage,

        #[Autowire('%app.gallery_image_public_path_prefix%')]
        string $galleryImagePublicPathPrefix,

        #[Autowire('%app.gallery_image_directory%')]
        string $galleryImageDirectory,

        #[Autowire('%app.no_gallery_image_path%')]
        string $noImagePath
    ) {
        $this->galleryImageStorage = $galleryImageStorage;
        $this->galleryImagePublicPathPrefix = $galleryImagePublicPathPrefix;
        $this->galleryImageDirectory = $galleryImageDirectory;
        $this->noImagePath = $noImagePath;
    }

    /**
     * @inheritDoc
     */
    public function getImageLastModified(GalleryImage $galleryImage): ?int
    {
        $fileName = $galleryImage->getFileName();

        if (!$this->galleryImageStorage->has($fileName))
        {
            return null;
        }

        return $this->galleryImageStorage->lastModified($fileName);
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
    public function getImagePublicUrl(?GalleryImage $galleryImage): string
    {
        $noImageUrl = $this->getNoImageUrl();

        if ($galleryImage === null)
        {
            return $noImageUrl;
        }

        $fileName = $galleryImage->getFileName();

        if (!$this->galleryImageStorage->has($fileName))
        {
            return $noImageUrl;
        }

        return $this->galleryImagePublicPathPrefix . $this->galleryImageDirectory . '/' . $fileName;
    }

    /**
     * @inheritDoc
     */
    public function removeFile(GalleryImage $galleryImage): void
    {
        $fileName = $galleryImage->getFileName();
        $this->galleryImageStorage->delete($fileName);
    }

    private function getNoImageUrl(): string
    {
        return $this->galleryImagePublicPathPrefix . $this->noImagePath;
    }
}