<?php

namespace App\Model\Event\Admin\PurchasableItemVariantValue;

use App\Model\Entity\PurchasableItemVariantValue;
use App\Model\Event\AbstractModelEvent;

class PurchasableItemVariantValueDeleteEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.purchasable_item_variant_value.delete';

    private PurchasableItemVariantValue $entity;

    public function __construct(PurchasableItemVariantValue $entity)
    {
        $this->entity = $entity;
    }

    public function getPurchasableItemVariantValue(): PurchasableItemVariantValue
    {
        return $this->entity;
    }

    public function setPurchasableItemVariantValue(PurchasableItemVariantValue $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}