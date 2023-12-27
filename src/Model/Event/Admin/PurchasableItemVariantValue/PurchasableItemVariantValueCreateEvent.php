<?php

namespace App\Model\Event\Admin\PurchasableItemVariantValue;

use App\Library\Data\Admin\PurchasableItemVariantValueData;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Entity\PurchasableItemVariantValue;
use App\Model\Event\AbstractModelEvent;

class PurchasableItemVariantValueCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.purchasable_item_variant_value.create';

    private PurchasableItemVariantValueData $data;

    private PurchasableItemVariant $purchasableItemVariant;

    private ?PurchasableItemVariantValue $entity = null;

    public function __construct(PurchasableItemVariantValueData $data, PurchasableItemVariant $purchasableItemVariant)
    {
        $this->data = $data;
        $this->purchasableItemVariant = $purchasableItemVariant;
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

    public function getPurchasableItemVariant(): PurchasableItemVariant
    {
        return $this->purchasableItemVariant;
    }

    public function setPurchasableItemVariant(PurchasableItemVariant $purchasableItemVariant): self
    {
        $this->purchasableItemVariant = $purchasableItemVariant;

        return $this;
    }

    public function getPurchasableItemVariantValue(): ?PurchasableItemVariantValue
    {
        return $this->entity;
    }

    public function setPurchasableItemVariantValue(?PurchasableItemVariantValue $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}