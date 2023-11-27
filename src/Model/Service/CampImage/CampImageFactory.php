<?php

namespace App\Model\Service\CampImage;

use App\Model\Entity\Camp;
use App\Model\Entity\CampImage;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @inheritDoc
 */
class CampImageFactory implements CampImageFactoryInterface
{
    private string $campImageUploadDirectory;

    public function __construct(string $campImageUploadDirectory)
    {
        $this->campImageUploadDirectory = $campImageUploadDirectory;
    }

    /**
     * @inheritDoc
     */
    public function createCampImage(File $file, int $priority, Camp $camp): CampImage
    {
        $extension = $file->guessExtension();
        $campImage = new CampImage($priority, $extension, $camp);
        $idString = $campImage
            ->getId()
            ->toRfc4122()
        ;

        $newFileName = $idString . '.' . $extension;
        $file->move($this->campImageUploadDirectory, $newFileName);

        return $campImage;
    }
}