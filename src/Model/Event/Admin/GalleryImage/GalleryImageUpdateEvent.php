<?php

namespace App\Model\Event\Admin\GalleryImage;

use App\Library\Data\Admin\GalleryImageData;
use App\Model\Entity\GalleryImage;
use App\Model\Event\AbstractModelEvent;

class GalleryImageUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.gallery_image.update';

    private GalleryImageData $data;

    private GalleryImage $entity;

    public function __construct(GalleryImageData $data, GalleryImage $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getGalleryImageData(): GalleryImageData
    {
        return $this->data;
    }

    public function setGalleryImageData(GalleryImageData $data): self
    {
        $this->data = $data;

        return $this;
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