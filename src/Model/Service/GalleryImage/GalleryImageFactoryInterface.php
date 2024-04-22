<?php

namespace App\Model\Service\GalleryImage;

use App\Model\Entity\GalleryImage;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Creates gallery image entities.
 */
interface GalleryImageFactoryInterface
{
    /**
     * Creates a gallery image entity for the given file.
     *
     * @param File $file
     * @return GalleryImage
     */
    public function createGalleryImage(File $file): GalleryImage;
}