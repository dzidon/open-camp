<?php

namespace App\Model\Event\Admin\User;

use App\Library\Data\Admin\ProfileData;
use App\Model\Entity\User;
use App\Model\Event\AbstractModelEvent;

class ProfileUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.profile.update';

    private ProfileData $data;

    private User $entity;

    public function __construct(ProfileData $data, User $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getProfileData(): ProfileData
    {
        return $this->data;
    }

    public function setProfileData(ProfileData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getUser(): User
    {
        return $this->entity;
    }

    public function setUser(User $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}