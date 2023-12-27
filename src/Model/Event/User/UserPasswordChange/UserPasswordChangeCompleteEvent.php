<?php

namespace App\Model\Event\User\UserPasswordChange;

use App\Library\Data\User\PlainPasswordData;
use App\Model\Entity\UserPasswordChange;
use App\Model\Event\AbstractModelEvent;
use App\Model\Library\UserPasswordChange\UserPasswordChangeCompletionResultInterface;

class UserPasswordChangeCompleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.user_password_change.complete';

    private PlainPasswordData $data;

    private UserPasswordChange $entity;

    private ?UserPasswordChangeCompletionResultInterface $result = null;

    public function __construct(PlainPasswordData $data, UserPasswordChange $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getPlainPasswordData(): PlainPasswordData
    {
        return $this->data;
    }

    public function setPlainPasswordData(PlainPasswordData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getUserPasswordChange(): UserPasswordChange
    {
        return $this->entity;
    }

    public function setUserPasswordChange(UserPasswordChange $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    public function getUserPasswordChangeCompletionResult(): ?UserPasswordChangeCompletionResultInterface
    {
        return $this->result;
    }

    public function setUserPasswordChangeCompletionResult(?UserPasswordChangeCompletionResultInterface $result): self
    {
        $this->result = $result;

        return $this;
    }
}