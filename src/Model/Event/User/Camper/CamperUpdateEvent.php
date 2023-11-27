<?php

namespace App\Model\Event\User\Camper;

use App\Library\Data\User\CamperData;
use App\Model\Entity\Camper;
use Symfony\Contracts\EventDispatcher\Event;

class CamperUpdateEvent extends Event
{
    public const NAME = 'model.user.camper.update';

    private CamperData $data;

    private Camper $entity;

    public function __construct(CamperData $data, Camper $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getCamperData(): CamperData
    {
        return $this->data;
    }

    public function setCamperData(CamperData $data): self
    {
        $this->data = $data;

        return $this;
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