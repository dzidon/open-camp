<?php

namespace App\Model\Event\User\Camper;

use App\Model\Entity\Camper;
use Symfony\Contracts\EventDispatcher\Event;

class CamperDeleteEvent extends Event
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