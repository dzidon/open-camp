<?php

namespace App\Model\Event\Admin\CampCategory;

use App\Library\Data\Admin\CampCategoryData;
use App\Model\Entity\CampCategory;
use App\Model\Event\AbstractModelEvent;

class CampCategoryCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.camp_category.create';

    private CampCategoryData $data;

    private ?CampCategory $entity = null;

    public function __construct(CampCategoryData $data)
    {
        $this->data = $data;
    }

    public function getCampCategoryData(): CampCategoryData
    {
        return $this->data;
    }

    public function setCampCategoryData(CampCategoryData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getCampCategory(): ?CampCategory
    {
        return $this->entity;
    }

    public function setCampCategory(?CampCategory $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}