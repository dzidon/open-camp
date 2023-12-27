<?php

namespace App\Model\Event\Admin\PurchasableItemVariantValue;

use App\Library\Data\Admin\PurchasableItemVariantValueData;
use App\Model\Entity\PurchasableItemVariantValue;
use App\Model\Event\AbstractModelEvent;

class PurchasableItemVariantValueUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.purchasable_item_variant_value.update';

    private PurchasableItemVariantValueData $data;

    private PurchasableItemVariantValue $entity;

    public function __construct(PurchasableItemVariantValueData $data, PurchasableItemVariantValue $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getPurchasableItemVariantValueData(): PurchasableItemVariantValueData
    {
        return $this->data;
    }

    public function setPurchasableItemVariantValueData(PurchasableItemVariantValueData $data): self
    {
        $this->data = $data;

        return $this;
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