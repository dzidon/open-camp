<?php

namespace App\Model\Event\User\Camper;

use App\Model\Entity\Camper;
use App\Model\Event\AbstractModelEvent;

class CamperDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.camper.delete';

    private Camper $entity;

    public function __construct(Camper $entity)
    {
        $this->entity = $entity;
    }

    public function getCamper(): Camper
    {
        return $this->entity;
    }

    public function setCamper(Camper $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}