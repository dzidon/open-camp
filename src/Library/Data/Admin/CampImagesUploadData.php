<?php

namespace App\Library\Data\Admin;

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