<?php

namespace App\Model\Event\Admin\User;

use App\Library\Data\Admin\UserData;
use Symfony\Contracts\EventDispatcher\Event;

class UserCreateEvent extends Event
{
    public const NAME = 'model.admin.user.create';

    private UserData $data;

    public function __construct(UserData $data)
    {
        $this->data = $data;
    }

    public function getUserData(): UserData
    {
        return $this->data;
    }

    public function setUserData(UserData $data): self
    {
        $this->data = $data;

        return $this;
    }
}