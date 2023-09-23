<?php

namespace App\Model\Module\CampCatalog\CampImage;

use App\Model\Entity\Camp;
use App\Model\Entity\CampImage;
use App\Model\Repository\CampImageRepositoryInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @inheritDoc
 */
class CampImageFactory implements CampImageFactoryInterface
{
    private CampImageRepositoryInterface $campImageRepository;

    private string $campImageUploadDirectory;

    public function __construct(CampImageRepositoryInterface $campImageRepository, string $campImageUploadDirectory)
    {
        $this->campImageRepository = $campImageRepository;

        $this->campImageUploadDirectory = $campImageUploadDirectory;
    }

    /**
     * @inheritDoc
     */
    public function createCampImage(File $file, int $priority, Camp $camp, bool $flush): CampImage
    {
        $extension = $file->guessExtension();
        $campImage = new CampImage($priority, $extension, $camp);
        $idString = $campImage
            ->getId()
            ->toRfc4122()
        ;

        $newFileName = $idString . '.' . $extension;
        $file->move($this->campImageUploadDirectory, $newFileName);
        $this->campImageRepository->saveCampImage($campImage, $flush);

        return $campImage;
    }
}