<?php

namespace App\Model\Event\User\UserPasswordChange;

use App\Library\Data\User\PlainPasswordData;
use App\Model\Entity\UserPasswordChange;
use App\Model\Library\UserPasswordChange\UserPasswordChangeCompletionResultInterface;
use Symfony\Contracts\EventDispatcher\Event;

class UserPasswordChangeCompletedEvent extends Event
{
    public const NAME = 'model.user.user_password_change.completed';

    private PlainPasswordData $data;

    private UserPasswordChange $entity;

    private UserPasswordChangeCompletionResultInterface $result;

    public function __construct(PlainPasswordData                           $data,
                                UserPasswordChange                          $entity,
                                UserPasswordChangeCompletionResultInterface $result)
    {
        $this->data = $data;
        $this->entity = $entity;
        $this->result = $result;
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

    public function getUserPasswordChangeCompletionResult(): UserPasswordChangeCompletionResultInterface
    {
        return $this->result;
    }

    public function setUserPasswordChangeCompletionResult(UserPasswordChangeCompletionResultInterface $result): self
    {
        $this->result = $result;

        return $this;
    }
}