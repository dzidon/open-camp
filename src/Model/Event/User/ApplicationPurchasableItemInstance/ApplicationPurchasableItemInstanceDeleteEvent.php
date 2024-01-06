<?php

namespace App\Model\Event\User\ApplicationPurchasableItemInstance;

use App\Model\Entity\ApplicationPurchasableItemInstance;
use App\Model\Event\AbstractModelEvent;

class ApplicationPurchasableItemInstanceDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.user.application_purchasable_item_instance.delete';

    private ApplicationPurchasableItemInstance $entity;

    public function __construct(ApplicationPurchasableItemInstance $entity)
    {
        $this->entity = $entity;
    }

    public function getApplicationPurchasableItemInstance(): ApplicationPurchasableItemInstance
    {
        return $this->entity;
    }

    public function setApplicationPurchasableItemInstance(ApplicationPurchasableItemInstance $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}