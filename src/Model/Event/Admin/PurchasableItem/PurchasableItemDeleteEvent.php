<?php

namespace App\Model\Event\Admin\PurchasableItem;

use App\Model\Entity\PurchasableItem;
use Symfony\Contracts\EventDispatcher\Event;

class PurchasableItemDeleteEvent extends Event
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