<?php

namespace App\Model\Event\User\User;

use App\Library\Data\User\ProfilePasswordChangeData;
use App\Model\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserPasswordChangeEvent extends Event
{
    public const NAME = 'model.user.user.password_change';

    private ProfilePasswordChangeData $data;

    private User $entity;

    public function __construct(ProfilePasswordChangeData $data, User $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getProfilePasswordChangeData(): ProfilePasswordChangeData
    {
        return $this->data;
    }

    public function setProfilePasswordChangeData(ProfilePasswordChangeData $data): self
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