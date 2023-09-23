<?php

namespace App\Model\Module\CampCatalog\CampImage;

use App\Model\Entity\Camp;
use App\Model\Entity\CampImage;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Creates camp image entities.
 */
interface CampImageFactoryInterface
{
    /**
     * Creates a camp image entity for the given file.
     *
     * @param File $file
     * @param int $priority
     * @param Camp $camp
     * @param bool $flush
     * @return CampImage
     */
    public function createCampImage(File $file, int $priority, Camp $camp, bool $flush): CampImage;
}