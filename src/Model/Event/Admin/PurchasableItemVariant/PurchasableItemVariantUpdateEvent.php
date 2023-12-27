<?php

namespace App\Model\Event\Admin\PurchasableItemVariant;

use App\Library\Data\Admin\PurchasableItemVariantData;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Event\AbstractModelEvent;

class PurchasableItemVariantUpdateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.purchasable_item_variant.update';

    private PurchasableItemVariantData $data;

    private PurchasableItemVariant $entity;

    public function __construct(PurchasableItemVariantData $data, PurchasableItemVariant $entity)
    {
        $this->data = $data;
        $this->entity = $entity;
    }

    public function getPurchasableItemVariantData(): PurchasableItemVariantData
    {
        return $this->data;
    }

    public function setPurchasableItemVariantData(PurchasableItemVariantData $data): self
    {
        $this->data = $data;

        return $this;
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