<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\GalleryImageData;
use App\Model\Entity\GalleryImage;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link GalleryImageData} to {@link GalleryImage} and vice versa.
 */
class GalleryImageDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof GalleryImageData && $entity instanceof GalleryImage;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var GalleryImageData $galleryImageData */
        /** @var GalleryImage $galleryImage */
        $galleryImageData = $data;
        $galleryImage = $entity;

        $galleryImageData->setGalleryImageCategory($galleryImage->getGalleryImageCategory());
        $galleryImageData->setIsHiddenInGallery($galleryImage->isHiddenInGallery());
        $galleryImageData->setIsInCarousel($galleryImage->isInCarousel());

        if ($galleryImage->isInCarousel())
        {
            $galleryImageData->setCarouselPriority($galleryImage->getCarouselPriority());
        }
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var GalleryImageData $galleryImageData */
        /** @var GalleryImage $galleryImage */
        $galleryImageData = $data;
        $galleryImage = $entity;

        $galleryImage->setGalleryImageCategory($galleryImageData->getGalleryImageCategory());
        $galleryImage->setIsHiddenInGallery($galleryImageData->isHiddenInGallery());
        $galleryImage->setIsInCarousel($galleryImageData->isInCarousel());

        if ($galleryImageData->isInCarousel())
        {
            $galleryImage->setCarouselPriority($galleryImageData->getCarouselPriority());
        }
        else
        {
            $galleryImage->setCarouselPriority(null);
        }
    }
}