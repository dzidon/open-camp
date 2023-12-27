<?php

namespace App\Model\Event\Admin\Camp;

use App\Library\Data\Admin\CampData;
use App\Model\Entity\Camp;
use App\Model\Event\AbstractModelEvent;

class CampUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.camp.update';

    private CampData $data;

    private Camp $entity;

    public function __construct(CampData $data, Camp $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getCampData(): CampData
    {
        return $this->data;
    }

    public function setCampData(CampData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getCamp(): Camp
    {
        return $this->entity;
    }

    public function setCamp(Camp $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}