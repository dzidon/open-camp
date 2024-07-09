<?php

namespace App\Model\Library\GalleryImage;

use App\Model\Entity\GalleryImage;

/**
 * Used for the gallery image browser.
 */
interface GalleryImageSurroundingsResultInterface
{
    /**
     * @return GalleryImage
     */
    public function getCurrentGalleryImage(): GalleryImage;

    /**
     * @return GalleryImage|null
     */
    public function getPreviousGalleryImage(): ?GalleryImage;

    /**
     * @return GalleryImage|null
     */
    public function getNextGalleryImage(): ?GalleryImage;
}