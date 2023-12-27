<?php

namespace App\Model\Event\Admin\PurchasableItemVariant;

use App\Model\Entity\PurchasableItemVariant;
use App\Model\Event\AbstractModelEvent;

class PurchasableItemVariantDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.purchasable_item_variant.delete';

    private PurchasableItemVariant $entity;

    public function __construct(PurchasableItemVariant $entity)
    {
        $this->entity = $entity;
    }

    public function getPurchasableItemVariant(): PurchasableItemVariant
    {
        return $this->entity;
    }

    public function setPurchasableItemVariant(PurchasableItemVariant $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}