<?php

namespace App\Model\Event\Admin\GalleryImageCategory;

use App\Model\Entity\GalleryImageCategory;
use App\Model\Event\AbstractModelEvent;

class GalleryImageCategoryDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.gallery_image_category.delete';

    private GalleryImageCategory $entity;

    public function __construct(GalleryImageCategory $entity)
    {
        $this->entity = $entity;
    }

    public function getGalleryImageCategory(): GalleryImageCategory
    {
        return $this->entity;
    }

    public function setGalleryImageCategory(GalleryImageCategory $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}