<?php

namespace App\Model\Service\GalleryImage;

use App\Model\Entity\GalleryImage;
use App\Model\Repository\GalleryImageRepositoryInterface;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @inheritDoc
 */
class GalleryImageFactory implements GalleryImageFactoryInterface
{
    private int $highestPriority = 0;

    private FilesystemOperator $galleryImageStorage;

    private GalleryImageRepositoryInterface $galleryImageRepository;

    public function __construct(FilesystemOperator $galleryImageStorage, GalleryImageRepositoryInterface $galleryImageRepository)
    {
        $this->galleryImageStorage = $galleryImageStorage;
        $this->galleryImageRepository = $galleryImageRepository;
    }

    /**
     * @inheritDoc
     */
    public function createGalleryImage(File $file): GalleryImage
    {
        $highestPriorityFromDb = (int) $this->galleryImageRepository->getHighestPriority();

        if ($highestPriorityFromDb > $this->highestPriority)
        {
            $this->highestPriority = $highestPriorityFromDb;
        }

        $this->highestPriority++;
        $priority = $this->highestPriority;
        $extension = $file->guessExtension();
        $galleryImage = new GalleryImage($extension, $priority);
        $newFileName = $galleryImage->getFileName();
        $contents = $file->getContent();

        $this->galleryImageStorage->write($newFileName, $contents);

        return $galleryImage;
    }
}