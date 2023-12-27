<?php

namespace App\Model\Event\Admin\PurchasableItem;

use App\Model\Entity\PurchasableItem;
use App\Model\Event\AbstractModelEvent;

class PurchasableItemDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.purchasable_item.delete';

    private PurchasableItem $entity;

    public function __construct(PurchasableItem $entity)
    {
        $this->entity = $entity;
    }

    public function getPurchasableItem(): PurchasableItem
    {
        return $this->entity;
    }

    public function setPurchasableItem(PurchasableItem $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}