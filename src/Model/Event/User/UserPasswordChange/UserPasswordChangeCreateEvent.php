<?php

namespace App\Model\Event\User\UserPasswordChange;

use App\Library\Data\User\PasswordChangeData;
use App\Model\Event\AbstractModelEvent;
use App\Model\Library\UserPasswordChange\UserPasswordChangeResultInterface;

class UserPasswordChangeCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.user_password_change.create';

    private PasswordChangeData $data;

    private ?UserPasswordChangeResultInterface $result = null;

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

    public function getUserPasswordChangeResult(): ?UserPasswordChangeResultInterface
    {
        return $this->result;
    }

    public function setUserPasswordChangeResult(?UserPasswordChangeResultInterface $result): self
    {
        $this->result = $result;

        return $this;
    }
}