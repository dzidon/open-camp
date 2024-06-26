<?php

namespace App\Model\Event\Admin\ApplicationPayment;

use App\Model\Entity\ApplicationPayment;
use App\Model\Event\AbstractModelEvent;

class ApplicationPaymentOfflineDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.application_payment.offline_delete';

    private ApplicationPayment $entity;

    public function __construct(ApplicationPayment $entity)
    {
        $this->entity = $entity;
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