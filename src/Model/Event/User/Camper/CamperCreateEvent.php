<?php

namespace App\Model\Event\User\Camper;

use App\Library\Data\Common\CamperData;
use App\Model\Entity\Camper;
use App\Model\Entity\User;
use App\Model\Event\AbstractModelEvent;

class CamperCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.camper.create';

    private CamperData $data;

    private User $user;

    private ?Camper $entity = null;

    public function __construct(CamperData $data, User $user)
    {
        $this->data = $data;
        $this->user = $user;
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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCamper(): ?Camper
    {
        return $this->entity;
    }

    public function setCamper(?Camper $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}