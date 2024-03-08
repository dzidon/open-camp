<?php

namespace App\Model\Event\User\ApplicationPayment;

use App\Model\Entity\Application;
use App\Model\Entity\ApplicationPayment;
use App\Model\Event\AbstractModelEvent;

class ApplicationPaymentOfflineDepositCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application_payment.offline_deposit_create';

    private Application $application;

    private ?ApplicationPayment $applicationPayment = null;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function setApplication(Application $application): self
    {
        $this->application = $application;

        return $this;
    }

    public function getApplicationPayment(): ?ApplicationPayment
    {
        return $this->applicationPayment;
    }

    public function setApplicationPayment(?ApplicationPayment $applicationPayment): self
    {
        $this->applicationPayment = $applicationPayment;

        return $this;
    }
}