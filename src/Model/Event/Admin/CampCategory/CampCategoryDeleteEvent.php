<?php

namespace App\Model\Event\Admin\CampCategory;

use App\Model\Entity\CampCategory;
use Symfony\Contracts\EventDispatcher\Event;

class CampCategoryDeleteEvent extends Event
{
    public const NAME = 'model.admin.camp_category.delete';

    private CampCategory $entity;

    public function __construct(CampCategory $entity)
    {
        $this->entity = $entity;
    }

    public function getCampCategory(): CampCategory
    {
        return $this->entity;
    }

    public function setCampCategory(CampCategory $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}