<?php

namespace App\Model\Event\Admin\PurchasableItemVariant;

use App\Library\Data\Admin\PurchasableItemVariantCreationData;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Event\AbstractModelEvent;

class PurchasableItemVariantCreateEvent extends AbstractModelEvent
{
    public const NAME = 'model.admin.purchasable_item_variant.create';

    private PurchasableItemVariantCreationData $data;

    private ?PurchasableItemVariant $entity = null;

    public function __construct(PurchasableItemVariantCreationData $data)
    {
        $this->data = $data;
    }

    public function getPurchasableItemVariantCreationData(): PurchasableItemVariantCreationData
    {
        return $this->data;
    }

    public function setPurchasableItemVariantCreationData(PurchasableItemVariantCreationData $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getPurchasableItemVariant(): ?PurchasableItemVariant
    {
        return $this->entity;
    }

    public function setPurchasableItemVariant(?PurchasableItemVariant $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
}