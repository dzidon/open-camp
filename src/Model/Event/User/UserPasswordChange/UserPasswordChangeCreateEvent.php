<?php

namespace App\Model\Event\User\UserPasswordChange;

use App\Library\Data\User\PasswordChangeData;
use Symfony\Contracts\EventDispatcher\Event;

class UserPasswordChangeCreateEvent extends Event
{
    public const NAME = 'model.user.user_password_change.create';

    private PasswordChangeData $data;

    public function __construct(PasswordChangeData $data)
    {
        $this->data = $data;
    }

    public function getPasswordChangeData(): PasswordChangeData
    {
        return $this->data;
    }

    public function setPasswordChangeData(PasswordChangeData $data): self
    {
        $this->data = $data;

        return $this;
    }
}