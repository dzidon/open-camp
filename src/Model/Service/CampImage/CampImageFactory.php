<?php

namespace App\Model\Service\CampImage;

use App\Model\Entity\Camp;
use App\Model\Entity\CampImage;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @inheritDoc
 */
class CampImageFactory implements CampImageFactoryInterface
{
    private FilesystemOperator $campImageStorage;

    public function __construct(FilesystemOperator $campImageStorage)
    {
        $this->campImageStorage = $campImageStorage;
    }

    /**
     * @inheritDoc
     */
    public function createCampImage(File $file, Camp $camp, int $priority): CampImage
    {
        $extension = $file->guessExtension();
        $campImage = new CampImage($priority, $extension, $camp);
        $newFileName = $campImage->getFileName();
        $contents = $file->getContent();

        $this->campImageStorage->write($newFileName, $contents);

        return $campImage;
    }
}