<?php

namespace App\Model\Event\Admin\CampCategory;

use App\Library\Data\Admin\CampCategoryData;
use Symfony\Contracts\EventDispatcher\Event;

class CampCategoryCreateEvent extends Event
{
    public const NAME = 'model.admin.camp_category.create';

    private CampCategoryData $data;

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
}