<?php

namespace App\Model\Event\Admin\PurchasableItemVariant;

use App\Library\Data\Admin\PurchasableItemVariantCreationData;
use Symfony\Contracts\EventDispatcher\Event;

class PurchasableItemVariantCreateEvent extends Event
{
    public const NAME = 'model.admin.purchasable_item_variant.create';

    private PurchasableItemVariantCreationData $data;

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
}