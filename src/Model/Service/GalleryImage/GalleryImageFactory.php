<?php

namespace App\Model\Service\GalleryImage;

use App\Model\Entity\GalleryImage;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @inheritDoc
 */
class GalleryImageFactory implements GalleryImageFactoryInterface
{
    private FilesystemOperator $galleryImageStorage;

    public function __construct(FilesystemOperator $galleryImageStorage)
    {
        $this->galleryImageStorage = $galleryImageStorage;
    }

    /**
     * @inheritDoc
     */
    public function createGalleryImage(File $file): GalleryImage
    {
        $extension = $file->guessExtension();
        $galleryImage = new GalleryImage($extension);
        $newFileName = $galleryImage->getFileName();
        $contents = $file->getContent();

        $this->galleryImageStorage->write($newFileName, $contents);

        return $galleryImage;
    }
}