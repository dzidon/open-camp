<?php

namespace App\Model\Event\User\UserRegistration;

use App\Library\Data\User\PlainPasswordData;
use App\Model\Entity\UserRegistration;
use App\Model\Library\UserRegistration\UserRegistrationCompletionResultInterface;
use Symfony\Contracts\EventDispatcher\Event;

class UserRegistrationCompletedEvent extends Event
{
    public const NAME = 'model.user.user_registration.completed';

    private PlainPasswordData $data;

    private UserRegistration $entity;

    private UserRegistrationCompletionResultInterface $result;

    public function __construct(PlainPasswordData                         $data,
                                UserRegistration                          $entity,
                                UserRegistrationCompletionResultInterface $result)
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

    public function getUserRegistration(): UserRegistration
    {
        return $this->entity;
    }

    public function setUserRegistration(UserRegistration $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    public function getUserRegistrationCompletionResult(): UserRegistrationCompletionResultInterface
    {
        return $this->result;
    }

    public function setUserRegistrationCompletionResult(UserRegistrationCompletionResultInterface $result): self
    {
        $this->result = $result;

        return $this;
    }
}