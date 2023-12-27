<?php

namespace App\Model\Event\Admin\CampDate;

use App\Library\Data\Admin\CampDateData;
use App\Model\Entity\CampDate;
use App\Model\Event\AbstractModelEvent;

class CampDateCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.camp_date.create';

    private CampDateData $data;

    private ?CampDate $entity = null;

    public function __construct(CampDateData $data)
    {
        $this->data = $data;
    }

    public function getCampDateData(): CampDateData
    {
        return $this->data;
    }

    public function setCampDateData(CampDateData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getCampDate(): ?CampDate
    {
        return $this->entity;
    }

    public function setCampDate(?CampDate $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}