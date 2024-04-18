<?php

namespace App\Model\Event\Admin\GalleryImageCategory;

use App\Library\Data\Admin\GalleryImageCategoryData;
use App\Model\Entity\GalleryImageCategory;
use App\Model\Event\AbstractModelEvent;

class GalleryImageCategoryCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.gallery_image_category.create';

    private GalleryImageCategoryData $data;

    private ?GalleryImageCategory $entity = null;

    public function __construct(GalleryImageCategoryData $data)
    {
        $this->data = $data;
    }

    public function getGalleryImageCategoryData(): GalleryImageCategoryData
    {
        return $this->data;
    }

    public function setGalleryImageCategoryData(GalleryImageCategoryData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getGalleryImageCategory(): ?GalleryImageCategory
    {
        return $this->entity;
    }

    public function setGalleryImageCategory(?GalleryImageCategory $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}