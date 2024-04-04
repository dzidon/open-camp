<?php

namespace App\Model\Event\Admin\ApplicationPayment;

use App\Library\Data\Admin\ApplicationPaymentData;
use App\Model\Entity\ApplicationPayment;
use App\Model\Event\AbstractModelEvent;

class ApplicationPaymentOfflineUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.application_payment.offline_update';

    private ApplicationPaymentData $data;

    private ApplicationPayment $entity;

    public function __construct(ApplicationPaymentData $data, ApplicationPayment $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
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

    public function getApplicationPayment(): ApplicationPayment
    {
        return $this->entity;
    }

    public function setApplicationPayment(ApplicationPayment $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}