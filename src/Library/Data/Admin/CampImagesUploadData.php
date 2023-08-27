<?php

namespace App\Library\Data\Admin;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class CampImagesUploadData
{
    /**
     * @var UploadedFile[] $images
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