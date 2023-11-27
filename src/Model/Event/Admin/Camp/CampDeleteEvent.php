<?php

namespace App\Model\Event\Admin\Camp;

use App\Model\Entity\Camp;
use Symfony\Contracts\EventDispatcher\Event;

class CampDeleteEvent extends Event
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