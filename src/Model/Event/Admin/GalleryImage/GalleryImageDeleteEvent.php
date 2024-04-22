<?php

namespace App\Model\Event\Admin\GalleryImage;

use App\Model\Entity\GalleryImage;
use App\Model\Event\AbstractModelEvent;

class GalleryImageDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.gallery_image.delete';

    private GalleryImage $entity;

    public function __construct(GalleryImage $entity)
    {
        $this->entity = $entity;
    }

    public function getGalleryImage(): GalleryImage
    {
        return $this->entity;
    }

    public function setGalleryImage(GalleryImage $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}