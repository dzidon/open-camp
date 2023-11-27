<?php

namespace App\Model\Event\User\UserPasswordChange;

use App\Library\Data\User\PasswordChangeData;
use App\Model\Library\UserPasswordChange\UserPasswordChangeResultInterface;
use Symfony\Contracts\EventDispatcher\Event;

class UserPasswordChangeCreatedEvent extends Event
{
    public const NAME = 'model.user.user_password_change.created';

    private PasswordChangeData $data;

    private UserPasswordChangeResultInterface $result;

    public function __construct(PasswordChangeData $data, UserPasswordChangeResultInterface $result)
    {
        $this->data = $data;
        $this->result = $result;
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

    public function getUserPasswordChangeResult(): UserPasswordChangeResultInterface
    {
        return $this->result;
    }

    public function setUserPasswordChangeResult(UserPasswordChangeResultInterface $result): self
    {
        $this->result = $result;

        return $this;
    }
}