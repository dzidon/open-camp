<?php

namespace App\Model\Event\Admin\CampImage;

use App\Model\Entity\CampImage;
use App\Model\Event\AbstractModelEvent;

class CampImageDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.camp_image.delete';

    private CampImage $entity;

    public function __construct(CampImage $entity)
    {
        $this->entity = $entity;
    }

    public function getCampImage(): CampImage
    {
        return $this->entity;
    }

    public function setCampImage(CampImage $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}