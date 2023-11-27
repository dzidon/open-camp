<?php

namespace App\Model\Event\User\Camper;

use App\Library\Data\User\CamperData;
use App\Model\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class CamperCreateEvent extends Event
{
    public const NAME = 'model.user.camper.create';

    private CamperData $data;

    private User $user;

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
}