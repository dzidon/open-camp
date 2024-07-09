<?php

namespace App\Library\Data\Admin;

use App\Library\Enum\Search\Data\Admin\GalleryImageSortEnum;
use App\Model\Entity\GalleryImageCategory;

class GalleryImageSearchData
{
    private false|null|GalleryImageCategory $galleryImageCategory = null;

    private GalleryImageSortEnum $sortBy = GalleryImageSortEnum::PRIORITY_DESC;

    private ?bool $isHiddenInGallery = null;

    private ?bool $isInCarousel = null;

    public function getGalleryImageCategory(): false|null|GalleryImageCategory
    {
        return $this->galleryImageCategory;
    }

    public function setGalleryImageCategory(false|null|GalleryImageCategory $galleryImageCategory): self
    {
        $this->galleryImageCategory = $galleryImageCategory;

        return $this;
    }
    
    public function getSortBy(): GalleryImageSortEnum
    {
        return $this->sortBy;
    }

    public function setSortBy(?GalleryImageSortEnum $sortBy): self
    {
        if ($sortBy === null)
        {
            $sortBy = GalleryImageSortEnum::PRIORITY_DESC;
        }

        $this->sortBy = $sortBy;

        return $this;
    }

    public function getIsHiddenInGallery(): ?bool
    {
        return $this->isHiddenInGallery;
    }

    public function setIsHiddenInGallery(?bool $isHiddenInGallery): self
    {
        $this->isHiddenInGallery = $isHiddenInGallery;

        return $this;
    }

    public function getIsInCarousel(): ?bool
    {
        return $this->isInCarousel;
    }

    public function setIsInCarousel(?bool $isInCarousel): self
    {
        $this->isInCarousel = $isInCarousel;

        return $this;
    }
}