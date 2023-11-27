<?php

namespace App\Model\Event\Admin\PurchasableItemVariantValue;

use App\Library\Data\Admin\PurchasableItemVariantValueData;
use Symfony\Contracts\EventDispatcher\Event;

class PurchasableItemVariantValueCreateEvent extends Event
{
    public const NAME = 'model.admin.purchasable_item_variant_value.create';

    private PurchasableItemVariantValueData $data;

    public function __construct(PurchasableItemVariantValueData $data)
    {
        $this->data = $data;
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
}