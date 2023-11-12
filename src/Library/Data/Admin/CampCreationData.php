<?php

namespace App\Library\Data\Admin;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class CampCreationData
{
    #[Assert\Valid]
    private CampData $campData;

    /**
     * @var File[] $images
     */
    #[Assert\All([
        new Assert\Image(),
    ])]
    private array $images = [];

    public function __construct()
    {
        $this->campData = new CampData();
    }

    public function getCampData(): CampData
    {
        return $this->campData;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): self
    {
        $this->images = $images;

        return $this;
    }
}