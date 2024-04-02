<?php

namespace App\Model\Event\Admin\ApplicationPurchasableItem;

use App\Model\Entity\ApplicationPurchasableItem;
use App\Model\Event\AbstractModelEvent;

class ApplicationPurchasableItemDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.application_purchasable_item.delete';

    private ApplicationPurchasableItem $entity;

    public function __construct(ApplicationPurchasableItem $entity)
    {
        $this->entity = $entity;
    }

    public function getApplicationPurchasableItem(): ApplicationPurchasableItem
    {
        return $this->entity;
    }

    public function setApplicationPurchasableItem(ApplicationPurchasableItem $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}