<?php

namespace App\Library\Data\Admin;

use App\Library\Constraint\Compound\SlugRequirements;
use App\Library\Constraint\UniqueGalleryImageCategory;
use App\Model\Entity\GalleryImageCategory;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueGalleryImageCategory]
class GalleryImageCategoryData
{
    private ?GalleryImageCategory $galleryImageCategory;

    #[Assert\Length(max: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[Assert\Length(max: 255)]
    #[SlugRequirements]
    #[Assert\NotBlank]
    private ?string $urlName = null;

    #[Assert\NotBlank]
    private ?int $priority = 0;

    private ?GalleryImageCategory $parent = null;

    public function __construct(?GalleryImageCategory $galleryImageCategory = null)
    {
        $this->galleryImageCategory = $galleryImageCategory;
    }

    public function getGalleryImageCategory(): ?GalleryImageCategory
    {
        return $this->galleryImageCategory;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrlName(): ?string
    {
        return $this->urlName;
    }

    public function setUrlName(?string $urlName): self
    {
        $this->urlName = $urlName;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getParent(): ?GalleryImageCategory
    {
        return $this->parent;
    }

    public function setParent(?GalleryImageCategory $parent): self
    {
        $this->parent = $parent;

        return $this;
    }
}