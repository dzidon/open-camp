<?php

namespace App\Model\Event\Admin\ImageContent;

use App\Library\Data\Admin\ImageContentData;
use App\Model\Entity\ImageContent;
use App\Model\Event\AbstractModelEvent;

class ImageContentUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.image_content.update';

    private ImageContentData $data;

    private ImageContent $entity;

    public function __construct(ImageContentData $data, ImageContent $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getImageContentData(): ImageContentData
    {
        return $this->data;
    }

    public function setImageContentData(ImageContentData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getImageContent(): ImageContent
    {
        return $this->entity;
    }

    public function setImageContent(ImageContent $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}