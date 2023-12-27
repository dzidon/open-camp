<?php

namespace App\Model\Event\Admin\Camp;

use App\Model\Entity\Camp;
use App\Model\Event\AbstractModelEvent;

class CampDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.camp.delete';

    private Camp $entity;

    public function __construct(Camp $entity)
    {
        $this->entity = $entity;
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