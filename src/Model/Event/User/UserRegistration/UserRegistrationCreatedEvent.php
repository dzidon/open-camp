<?php

namespace App\Model\Event\User\UserRegistration;

use App\Library\Data\User\RegistrationData;
use App\Model\Library\UserRegistration\UserRegistrationResultInterface;
use Symfony\Contracts\EventDispatcher\Event;

class UserRegistrationCreatedEvent extends Event
{
    public const NAME = 'model.user.user_registration.created';

    private RegistrationData $data;

    private UserRegistrationResultInterface $result;

    public function __construct(RegistrationData $data, UserRegistrationResultInterface $result)
    {
        $this->data = $data;
        $this->result = $result;
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

    public function getUserRegistrationResult(): UserRegistrationResultInterface
    {
        return $this->result;
    }

    public function setUserRegistrationResult(UserRegistrationResultInterface $result): self
    {
        $this->result = $result;

        return $this;
    }
}