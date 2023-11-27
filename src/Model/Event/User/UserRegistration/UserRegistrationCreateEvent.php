<?php

namespace App\Model\Event\User\UserRegistration;

use App\Library\Data\User\RegistrationData;
use Symfony\Contracts\EventDispatcher\Event;

class UserRegistrationCreateEvent extends Event
{
    public const NAME = 'model.user.user_registration.create';

    private RegistrationData $data;

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
}