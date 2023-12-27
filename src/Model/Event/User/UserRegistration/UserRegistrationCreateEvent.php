<?php

namespace App\Model\Event\User\UserRegistration;

use App\Library\Data\User\RegistrationData;
use App\Model\Event\AbstractModelEvent;
use App\Model\Library\UserRegistration\UserRegistrationResultInterface;

class UserRegistrationCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.user_registration.create';

    private RegistrationData $data;

    private ?UserRegistrationResultInterface $result = null;

    public function __construct(RegistrationData $data)
    {
        $this->data = $data;
    }

    public function getRegistrationData(): RegistrationData
    {
        return $this->data;
    }

    public function setRegistrationData(RegistrationData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getUserRegistrationResult(): ?UserRegistrationResultInterface
    {
        return $this->result;
    }

    public function setUserRegistrationResult(?UserRegistrationResultInterface $result): self
    {
        $this->result = $result;

        return $this;
    }
}