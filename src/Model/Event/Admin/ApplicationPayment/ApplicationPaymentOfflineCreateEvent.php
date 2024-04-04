<?php

namespace App\Model\Event\Admin\ApplicationPayment;

use App\Library\Data\Admin\ApplicationPaymentData;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationPayment;
use App\Model\Event\AbstractModelEvent;

class ApplicationPaymentOfflineCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.application_payment.offline_create';

    private ApplicationPaymentData $data;

    private Application $application;

    private ?ApplicationPayment $entity = null;

    public function __construct(ApplicationPaymentData $data, Application $application)
    {
        $this->data = $data;
        $this->application = $application;
    }

    public function getApplicationPaymentData(): ApplicationPaymentData
    {
        return $this->data;
    }

    public function setApplicationPaymentData(ApplicationPaymentData $data): self
    {
        $this->data = $data;

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
        return $this->entity;
    }

    public function setApplicationPayment(?ApplicationPayment $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}