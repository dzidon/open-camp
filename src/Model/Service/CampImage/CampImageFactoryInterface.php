<?php

namespace App\Model\Service\CampImage;

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
     * @param Camp $camp
     * @param int $priority
     * @return CampImage
     */
    public function createCampImage(File $file, Camp $camp, int $priority): CampImage;
}