<?php

namespace App\Model\Service\GalleryImage;

use App\Model\Entity\GalleryImage;

/**
 * Helper service for gallery image files.
 */
interface GalleryImageFilesystemInterface
{
    /**
     * Gets the timestamp of last modification.
     *
     * @param GalleryImage $galleryImage
     * @return int|null
     */
    public function getImageLastModified(GalleryImage $galleryImage): ?int;

    /**
     * Returns true if the given URL is equal to the gallery image placeholder.
     *
     * @param string $publicUrl
     * @return bool
     */
    public function isUrlPlaceholder(string $publicUrl): bool;

    /**
     * Gets the public path to the given gallery image.
     *
     * @param null|GalleryImage $galleryImage
     * @return string
     */
    public function getImagePublicUrl(?GalleryImage $galleryImage): string;

    /**
     * Removes the file of the given gallery image.
     *
     * @param GalleryImage $galleryImage
     * @return void
     */
    public function removeFile(GalleryImage $galleryImage): void;
}