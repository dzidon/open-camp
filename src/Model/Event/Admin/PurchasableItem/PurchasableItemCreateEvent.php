<?php

namespace App\Model\Event\Admin\PurchasableItem;

use App\Library\Data\Admin\PurchasableItemData;
use App\Model\Entity\PurchasableItem;
use App\Model\Event\AbstractModelEvent;

class PurchasableItemCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.purchasable_item.create';

    private PurchasableItemData $data;

    private ?PurchasableItem $entity = null;

    public function __construct(PurchasableItemData $data)
    {
        $this->data = $data;
    }

    public function getPurchasableItemData(): PurchasableItemData
    {
        return $this->data;
    }

    public function setPurchasableItemData(PurchasableItemData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getPurchasableItem(): ?PurchasableItem
    {
        return $this->entity;
    }

    public function setPurchasableItem(?PurchasableItem $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}