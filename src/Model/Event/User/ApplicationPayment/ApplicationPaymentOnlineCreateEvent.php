<?php

namespace App\Model\Event\User\ApplicationPayment;

use App\Model\Entity\Application;
use App\Model\Entity\ApplicationPayment;
use App\Model\Enum\Entity\ApplicationPaymentTypeEnum;
use App\Model\Event\AbstractModelEvent;

class ApplicationPaymentOnlineCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application_payment.online_create';

    private ApplicationPaymentTypeEnum $type;

    private Application $application;

    private ?ApplicationPayment $applicationPayment = null;

    public function __construct(ApplicationPaymentTypeEnum $type, Application $application)
    {
        $this->type = $type;
        $this->application = $application;
    }

    public function getType(): ApplicationPaymentTypeEnum
    {
        return $this->type;
    }

    public function setType(ApplicationPaymentTypeEnum $type): self
    {
        $this->type = $type;

        return $this;
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