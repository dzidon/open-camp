<?php

namespace App\Library\Data\Admin;

use App\Model\Entity\GalleryImageCategory;
use LogicException;
use Symfony\Component\Validator\Constraints as Assert;

class GalleryImageData
{
    /**
     * @var GalleryImageCategory[]
     */
    private array $galleryImageCategories;

    #[Assert\Choice(callback: 'getGalleryImageCategories')]
    private ?GalleryImageCategory $galleryImageCategory = null;

    private bool $isHiddenInGallery = false;

    private bool $isInCarousel = false;

    #[Assert\When(
        expression: 'this.isInCarousel()',
        constraints: [
            new Assert\NotBlank(),
        ],
    )]
    private ?int $carouselPriority = 0;

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

    public function getCarouselPriority(): ?int
    {
        return $this->carouselPriority;
    }

    public function setCarouselPriority(?int $carouselPriority): self
    {
        $this->carouselPriority = $carouselPriority;

        return $this;
    }
}