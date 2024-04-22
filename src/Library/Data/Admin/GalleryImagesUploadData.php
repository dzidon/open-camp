<?php

namespace App\Library\Data\Admin;

use App\Model\Entity\GalleryImageCategory;
use LogicException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class GalleryImagesUploadData
{
    /**
     * @var GalleryImageCategory[]
     */
    private array $galleryImageCategories;

    /**
     * @var File[] $images
     */
    #[Assert\All([
        new Assert\Image(),
    ])]
    #[Assert\NotBlank]
    private array $images = [];

    #[Assert\Choice(callback: 'getGalleryImageCategories')]
    private ?GalleryImageCategory $galleryImageCategory = null;

    private bool $isHiddenInGallery = false;

    private bool $isInCarousel = false;

    public function __construct(array $galleryImageCategories = [])
    {
        foreach ($galleryImageCategories as $galleryImageCategory)
        {
            if (!$galleryImageCategory instanceof GalleryImageCategory)
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain instances of %s.', __METHOD__, GalleryImageCategory::class)
                );
            }
        }

        $this->galleryImageCategories = $galleryImageCategories;
    }

    public function getGalleryImageCategories(): array
    {
        return $this->galleryImageCategories;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): self
    {
        foreach ($images as $image)
        {
            if (!$image instanceof File)
            {
                throw new LogicException(
                    sprintf('Array passed to %s must only contain instances of %s.', __METHOD__, File::class)
                );
            }
        }

        $this->images = $images;

        return $this;
    }

    public function getGalleryImageCategory(): ?GalleryImageCategory
    {
        return $this->galleryImageCategory;
    }

    public function setGalleryImageCategory(?GalleryImageCategory $galleryImageCategory): self
    {
        $this->galleryImageCategory = $galleryImageCategory;

        return $this;
    }

    public function isHiddenInGallery(): bool
    {
        return $this->isHiddenInGallery;
    }

    public function setIsHiddenInGallery(bool $isHiddenInGallery): self
    {
        $this->isHiddenInGallery = $isHiddenInGallery;

        return $this;
    }

    public function isInCarousel(): bool
    {
        return $this->isInCarousel;
    }

    public function setIsInCarousel(bool $isInCarousel): self
    {
        $this->isInCarousel = $isInCarousel;

        return $this;
    }
}