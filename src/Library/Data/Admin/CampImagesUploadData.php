<?php

namespace App\Library\Data\Admin;

use App\Model\Entity\Camp;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class CampImagesUploadData
{
    /**
     * @var File[] $images
     */
    #[Assert\All([
        new Assert\Image(),
    ])]
    #[Assert\NotBlank]
    private array $images = [];

    private Camp $camp;

    public function __construct(Camp $camp)
    {
        $this->camp = $camp;
    }

    public function getCamp(): Camp
    {
        return $this->camp;
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