<?php

namespace App\Model\Module\CampCatalog\CampImage;

use App\Model\Entity\CampImage;

/**
 * Helper service for camp image files.
 */
interface CampImageFilesystemInterface
{
    /**
     * Gets the path to the given camp image.
     *
     * @param null|CampImage $campImage
     * @return string
     */
    public function getFilePath(?CampImage $campImage): string;

    /**
     * Removes the file of the given camp image.
     *
     * @param CampImage $campImage
     * @return void
     */
    public function removeFile(CampImage $campImage): void;
}