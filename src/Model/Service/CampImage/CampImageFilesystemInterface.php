<?php

namespace App\Model\Service\CampImage;

use App\Model\Entity\CampImage;

/**
 * Helper service for camp image files.
 */
interface CampImageFilesystemInterface
{
    /**
     * Gets the timestamp of last modification.
     *
     * @param CampImage $campImage
     * @return int|null
     */
    public function getImageLastModified(CampImage $campImage): ?int;

    /**
     * Returns true if the given URL is equal to the camp image placeholder.
     *
     * @param string $publicUrl
     * @return bool
     */
    public function isUrlPlaceholder(string $publicUrl): bool;

    /**
     * Gets the public path to the given camp image.
     *
     * @param null|CampImage $campImage
     * @return string
     */
    public function getImagePublicUrl(?CampImage $campImage): string;

    /**
     * Removes the file of the given camp image.
     *
     * @param CampImage $campImage
     * @return void
     */
    public function removeFile(CampImage $campImage): void;
}