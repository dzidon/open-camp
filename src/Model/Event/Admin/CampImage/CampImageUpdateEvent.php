<?php

namespace App\Model\Event\Admin\CampImage;

use App\Library\Data\Admin\CampImageData;
use App\Model\Entity\CampImage;
use Symfony\Contracts\EventDispatcher\Event;

class CampImageUpdateEvent extends Event
{
    public const NAME = 'model.admin.camp_image.update';

    private CampImageData $data;

    private CampImage $entity;

    public function __construct(CampImageData $data, CampImage $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getCampImageData(): CampImageData
    {
        return $this->data;
    }

    public function setCampImageData(CampImageData $data): self
    {
        $this->data = $data;

        return $this;
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