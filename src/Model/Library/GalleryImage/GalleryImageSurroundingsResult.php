<?php

namespace App\Model\Library\GalleryImage;

use App\Model\Entity\GalleryImage;

/**
 * @inheritDoc
 */
class GalleryImageSurroundingsResult implements GalleryImageSurroundingsResultInterface
{
    private GalleryImage $currentGalleryImage;

    private ?GalleryImage $previousGalleryImage;

    private ?GalleryImage $nextGalleryImage;

    public function __construct(GalleryImage $currentGalleryImage, ?GalleryImage $previousGalleryImage, ?GalleryImage $nextGalleryImage)
    {
        $this->currentGalleryImage = $currentGalleryImage;
        $this->previousGalleryImage = $previousGalleryImage;
        $this->nextGalleryImage = $nextGalleryImage;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentGalleryImage(): GalleryImage
    {
        return $this->currentGalleryImage;
    }

    /**
     * @inheritDoc
     */
    public function getPreviousGalleryImage(): ?GalleryImage
    {
        return $this->previousGalleryImage;
    }

    /**
     * @inheritDoc
     */
    public function getNextGalleryImage(): ?GalleryImage
    {
        return $this->nextGalleryImage;
    }
}