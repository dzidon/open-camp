<?php

namespace App\Model\Event\User\ApplicationPayment;

use App\Model\Entity\ApplicationPayment;
use App\Model\Event\AbstractModelEvent;

class ApplicationPaymentOnlineUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application_payment.online_update';

    private ApplicationPayment $applicationPayment;

    private string $newState;

    public function __construct(ApplicationPayment $applicationPayment, string $newState)
    {
        $this->applicationPayment = $applicationPayment;
        $this->newState = $newState;
    }

    public function getApplicationPayment(): ApplicationPayment
    {
        return $this->applicationPayment;
    }

    public function setApplicationPayment(ApplicationPayment $applicationPayment): self
    {
        $this->applicationPayment = $applicationPayment;

        return $this;
    }

    public function getNewState(): string
    {
        return $this->newState;
    }

    public function setNewState(string $newState): self
    {
        $this->newState = $newState;

        return $this;
    }
}